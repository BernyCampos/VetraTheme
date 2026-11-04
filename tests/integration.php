<?php

declare(strict_types=1);

$completed = false;
register_shutdown_function(static function () use (&$completed): void {
    if (!$completed) {
        fwrite(STDERR, "Integration suite did not finish successfully.\n");
        exit(1);
    }
});

// Run against an isolated checkout only: php tests/integration.php /path/to/panel
// No fake Filament classes: this bootstraps the actual locked dependencies.
$panelPath = realpath($argv[1] ?? '') ?: throw new RuntimeException('Supply isolated Pelican path');
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=array');
putenv('APP_KEY=base64:' . base64_encode(str_repeat('v', 32)));
$loader = require $panelPath . '/vendor/autoload.php';
$loader->addPsr4('Vetra\\Theme\\', dirname(__DIR__) . '/src/');
$app = require $panelPath . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app['view']->addNamespace('vetra-theme', dirname(__DIR__) . '/resources/views');
$app['translator']->addNamespace('vetra-theme', dirname(__DIR__) . '/lang');
config(['vetra-theme' => Vetra\Theme\Appearance::DEFAULTS]);

$checks = 0;
$check = static function (bool $ok, string $message) use (&$checks): void {
    if (!$ok) {
        throw new RuntimeException($message);
    }
    $checks++;
};
$plugin = new Vetra\Theme\VetraThemePlugin();
$check($plugin instanceof App\Contracts\Plugins\HasPluginSettings, 'Settings contract');
foreach (['admin', 'app', 'server'] as $id) {
    $panel = Filament\Panel::make()->id($id);
    $plugin->register($panel);
    $plugin->boot($panel);
    $check($panel->getDefaultThemeMode() === Filament\Enums\ThemeMode::Dark, "$id mode");
    $check($panel->getSidebarWidth() === '240px', "$id sidebar");
}
$schema = $plugin->getSettingsForm();
$check(count($schema) === 5, 'Settings form constructed with actual Filament');
$check(!Illuminate\Support\Facades\Validator::make(Vetra\Theme\Appearance::DEFAULTS, Vetra\Theme\SettingsForm::rules())->fails(), 'Default validation');
foreach (['background' => 'https://evil.test/a.png', 'primary' => '#fff;}', 'opacity' => 10, 'blur' => 999, 'mode' => 'bad'] as $key => $value) {
    $check(Illuminate\Support\Facades\Validator::make(array_replace(Vetra\Theme\Appearance::DEFAULTS, [$key => $value]), Vetra\Theme\SettingsForm::rules())->fails(), "Reject $key");
}
$html = view('vetra-theme::preview', ['settings' => Vetra\Theme\Appearance::DEFAULTS])->render();
$check(str_contains($html, 'vetra-preview-surface'), 'Blade preview renders');
$html = view('vetra-theme::styles', ['stylesheet' => '/* bundled theme css */', 'css' => '--vetra-primary:#739fe3;'])->render();
$check(str_contains($html, 'id="vetra-theme"') && str_contains($html, 'bundled theme css') && !str_contains($html, '@vite'), 'Styles render without a Vite manifest lookup');
$html = view('vetra-theme::ambient', ['settings' => Vetra\Theme\Appearance::DEFAULTS, 'panelId' => 'server'])->render();
$check(str_contains($html, 'data-side-charts="true"'), 'Blade ambient renders');
$server = new stdClass();
$server->name = '<script>alert(1)</script>';
$server->description = '<img src=x onerror=alert(1)>';
$server->allocation = null;
$html = view('vetra-theme::server-context', compact('server'))->render();
$check(!str_contains($html, '<script>') && !str_contains($html, '<img '), 'Tenant data HTML escaped');

// Load/save the official environment trait against a disposable .env in temp.
Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
$ordinary = App\Models\User::factory()->create();
$root = App\Models\User::factory()->create();
App\Models\Role::firstOrCreate(['name' => App\Models\Role::ROOT_ADMIN]);
$root->assignRole(App\Models\Role::ROOT_ADMIN);
$app['auth']->setUser($ordinary);
$denied = false;
try {
    $plugin->saveSettings(Vetra\Theme\Appearance::DEFAULTS);
} catch (Illuminate\Auth\Access\AuthorizationException) {
    $denied = true;
}
$check($denied, 'Ordinary user cannot save global settings');
$app['auth']->setUser($root);
$temporary = sys_get_temp_dir() . '/vetra-test-' . bin2hex(random_bytes(6));
mkdir($temporary);
$app->useEnvironmentPath($temporary);
file_put_contents($temporary . '/.env', "APP_NAME=Unchanged\n");
$storage = new Vetra\Theme\BackgroundStorage($temporary . '/background', $temporary . '/backup');
$check(!is_dir($temporary . '/background') && !is_dir($temporary . '/backup'), 'Storage initialization does not create directories');
$app->instance(Vetra\Theme\BackgroundStorage::class, $storage);
try {
    $variables = [];
    foreach (Vetra\Theme\Appearance::DEFAULTS as $key => $value) {
        $variables['VETRA_THEME_' . strtoupper($key)] = $value;
    }
    $variables['VETRA_THEME_NOTICE_MARKDOWN'] = base64_encode('Maintenance "scheduled" #1 ${APP_KEY}');
    $plugin->writeToEnvironment($variables);
    $saved = Dotenv\Dotenv::parse(file_get_contents($temporary . '/.env'));
    $check($saved['APP_NAME'] === 'Unchanged', 'Existing environment preserved');
    $check($saved['VETRA_THEME_PRIMARY'] === '#739fe3', 'Color persisted');
    $check($saved['VETRA_THEME_NOTICE_MARKDOWN'] === $variables['VETRA_THEME_NOTICE_MARKDOWN'], 'Encoded environment text safely round-trips');
    $plugin->saveSettings(array_replace(Vetra\Theme\Appearance::DEFAULTS, ['opacity' => 91]));
    $saved = Dotenv\Dotenv::parse(file_get_contents($temporary . '/.env'));
    $check($saved['VETRA_THEME_OPACITY'] === '91', 'Authorized settings persist through real plugin method');
    $colors = array_fill_keys(array_keys(Vetra\Theme\Appearance::COLORS), '#456789');
    $plugin->saveSettings(array_replace(Vetra\Theme\Appearance::DEFAULTS, $colors));
    $saved = Dotenv\Dotenv::parse(file_get_contents($temporary . '/.env'));
    $check(count(array_filter(array_keys($colors), fn ($key) => $saved['VETRA_THEME_' . strtoupper($key)] === '#456789')) === count($colors), 'All palette colors persist');
    $check(Illuminate\Support\Facades\Validator::make(array_replace(Vetra\Theme\Appearance::DEFAULTS, ['dark_surface' => '#fff;display:none']), Vetra\Theme\SettingsForm::rules())->fails(), 'Custom palette rejects CSS injection');
    $markdown = "**Mantenimiento**\n\n- *Actualización*\n- [Estado](https://example.com)\n\n`código`";
    // Literal environment-looking text and multiline edits must survive repeated saves.
    $markdown .= '\n${APP_KEY} # "quoted"';
    foreach ([$markdown, $markdown . "\n\nSegundo párrafo"] as $notice) {
        $plugin->saveSettings(array_replace(Vetra\Theme\Appearance::DEFAULTS, ['notice' => $notice]));
        $saved = Dotenv\Dotenv::parse(file_get_contents($temporary . '/.env'));
        $check(base64_decode($saved['VETRA_THEME_NOTICE_MARKDOWN']) === $notice && $saved['APP_NAME'] === 'Unchanged', 'Multiline Markdown round-trips on repeated saves');
    }
    Illuminate\Support\Env::getRepository()->set('VETRA_THEME_NOTICE_MARKDOWN', base64_encode('0'));
    $loadedConfig = require dirname(__DIR__) . '/config/vetra-theme.php';
    $check($loadedConfig['notice'] === '0', 'Notice zero is preserved when loading configuration');
    Illuminate\Support\Env::getRepository()->clear('VETRA_THEME_NOTICE_MARKDOWN');
    $html = view('vetra-theme::notice', ['notice' => $markdown])->render();
    $check(str_contains($html, '<strong>Mantenimiento</strong>') && str_contains($html, '<ul>') && str_contains($html, 'href="https://example.com"'), 'Markdown formatting renders through real Blade');
    foreach (['<script>alert(1)</script>', '<img src=x onerror=alert(1)>', '[x](javascript:alert%281%29)', '[x](data:text/html,evil)', '![image](https://example.com/tracker.png)'] as $attack) {
        $html = (string) Vetra\Theme\Notice::render($attack);
        $check(!preg_match('/<script|<img|onerror|javascript:|data:text/i', $html), 'Markdown sanitization rejects unsafe content');
    }
    $plugin->saveSettings(['restore_defaults' => true]);
    $saved = Dotenv\Dotenv::parse(file_get_contents($temporary . '/.env'));
    $check($saved['VETRA_THEME_OPACITY'] === '82' && base64_decode($saved['VETRA_THEME_NOTICE_MARKDOWN']) === '', 'Restore persists all defaults');

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX1sAAAAASUVORK5CYII=');
    foreach (glob(__DIR__ . '/fixtures/background.*') as $fixture) {
        $upload = Illuminate\Http\UploadedFile::fake()->createWithContent(basename($fixture), file_get_contents($fixture));
        [$contents, $extension] = Vetra\Theme\BackgroundImage::read($upload);
        $check($contents === file_get_contents($fixture) && isset(Vetra\Theme\BackgroundImage::TYPES[$extension]), 'Raster fixture accepted unchanged: ' . basename($fixture));
    }
    $image = Illuminate\Http\UploadedFile::fake()->createWithContent('my background.png', $png);
    $plugin->saveSettings(Vetra\Theme\Appearance::DEFAULTS + ['background_upload' => $image]);
    $background = config('vetra-theme.background');
    $check((bool) preg_match('~^/vetra-theme/background/[a-f0-9]{40}\.png$~', $background), 'Generated safe image filename');
    $disk = Illuminate\Support\Facades\Storage::build(['driver' => 'local', 'root' => $temporary . '/background']);
    $backup = Illuminate\Support\Facades\Storage::build(['driver' => 'local', 'root' => $temporary . '/backup']);
    $path = basename($background);
    $check($disk->get($path) === $png, 'Image saved through Storage');
    $check($backup->get($path) === $png, 'Durable backup saved');
    $response = $storage->response($path);
    $check($response->headers->get('Content-Type') === 'image/png' && $response->headers->get('X-Content-Type-Options') === 'nosniff', 'Read endpoint serves explicit image headers');
    $disk->delete($path);
    rmdir($temporary . '/background');
    $check($storage->response($path)->getStatusCode() === 200, 'Backup remains readable when update replaces plugin files');
    $check(!is_dir($temporary . '/background'), 'Serving backup does not write to the plugin directory');
    $disk->put($path, $png);
    foreach (['../.env', str_repeat('a', 40) . '.php', str_repeat('a', 40) . '.png'] as $badPath) {
        $denied = false;
        try { $storage->response($badPath); } catch (Symfony\Component\HttpKernel\Exception\NotFoundHttpException) { $denied = true; }
        $check($denied, 'Read endpoint rejects missing or unsafe filename');
    }
    $saved = Dotenv\Dotenv::parse(file_get_contents($temporary . '/.env'));
    $check($saved['VETRA_THEME_BACKGROUND'] === $background, 'Uploaded image path persisted');
    $check(str_contains(view('vetra-theme::background-preview', compact('background'))->render(), $background), 'Saved image preview renders');

    // Docker/package deployments may mount the plugin directory read-only.
    // A regular file as the local root deterministically makes the mirror fail.
    $blockedPluginRoot = $temporary . '/read-only-plugin-root';
    file_put_contents($blockedPluginRoot, 'not a directory');
    $persistentStorage = new Vetra\Theme\BackgroundStorage($blockedPluginRoot, $temporary . '/persistent-background');
    $persistentPath = $persistentStorage->store(Illuminate\Http\UploadedFile::fake()->createWithContent('persistent.png', $png));
    $persistentFilename = basename($persistentPath);
    $persistentDisk = Illuminate\Support\Facades\Storage::build(['driver' => 'local', 'root' => $temporary . '/persistent-background']);
    $check($persistentDisk->get($persistentFilename) === $png, 'Read-only plugin directory preserves the persistent copy');
    $check($persistentStorage->response($persistentFilename)->getStatusCode() === 200, 'Persistent background remains publicly readable');
    $persistentStorage->discard($persistentFilename);
    rmdir($temporary . '/persistent-background');
    unlink($blockedPluginRoot);

    foreach ([
        '../existing.png',
        Illuminate\Http\UploadedFile::fake()->createWithContent('fake.png', '<?php echo 1;'),
        Illuminate\Http\UploadedFile::fake()->createWithContent('x.svg', '<!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg>&x;</svg>'),
        Illuminate\Http\UploadedFile::fake()->createWithContent('large.png', $png)->size(5121),
    ] as $invalid) {
        $check(Illuminate\Support\Facades\Validator::make(Vetra\Theme\Appearance::DEFAULTS + ['background_upload' => $invalid], Vetra\Theme\SettingsForm::rules())->fails(), 'Reject unsafe upload');
    }
    $app['auth']->setUser($ordinary);
    $denied = false;
    try {
        $plugin->saveSettings(Vetra\Theme\Appearance::DEFAULTS + ['background_upload' => $image]);
    } catch (Illuminate\Auth\Access\AuthorizationException) {
        $denied = true;
    }
    $check($denied && count($disk->allFiles()) === 1, 'Unauthorized upload cannot write a file');
    $app['auth']->setUser($root);
    $plugin->saveSettings(array_replace(Vetra\Theme\Appearance::DEFAULTS, ['background' => $background, 'remove_background' => true]));
    $check(config('vetra-theme.background') === '' && $disk->exists($path), 'Remove background only detaches; preserves stored image');
    $plugin->saveSettings(Vetra\Theme\Appearance::DEFAULTS + ['restore_defaults' => true, 'background_upload' => $image]);
    $check(count($disk->allFiles()) === 1 && config('vetra-theme.background') === '', 'Restore does not store a pending upload');
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" onload="alert(1)"><script>alert(1)</script><image href="https://evil.test/tracker.png"/><rect width="10" height="10" fill="red"/></svg>';
    $plugin->saveSettings(Vetra\Theme\Appearance::DEFAULTS + ['background_upload' => Illuminate\Http\UploadedFile::fake()->createWithContent('vector.svg', $svg)]);
    $svgPath = basename(config('vetra-theme.background'));
    $safe = $disk->get($svgPath);
    $check(str_contains($safe, '<rect') && !preg_match('/onload|<script|evil\.test/', $safe), 'SVG sanitized before both copies are written');
    $check($backup->get($svgPath) === $safe, 'SVG backup also sanitized');
    // A failed settings write must not orphan newly uploaded files.
    $app->loadEnvironmentFrom('.missing-env');
    $failed = false;
    try { $plugin->saveSettings(Vetra\Theme\Appearance::DEFAULTS + ['background_upload' => $image]); } catch (RuntimeException) { $failed = true; }
    $app->loadEnvironmentFrom('.env');
    $check($failed && count($disk->allFiles()) === 2 && count($backup->allFiles()) === 2, 'Environment failure rolls back both image copies');
    $storage->discard($path);
    $storage->discard($svgPath);
    rmdir($temporary . '/background');
    rmdir($temporary . '/backup');
} finally {
    unlink($temporary . '/.env');
    rmdir($temporary);
}
echo "PASS: $checks real Laravel/Filament integration checks\n";
$completed = true;
