import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, existsSync } from 'node:fs';

const read = (path) => readFileSync(new URL(`../${path}`, import.meta.url), 'utf8');
const manifest = JSON.parse(read('plugin.json'));
test('Pelican manifest, namespace and panels', () => {
    assert.equal(manifest.id, 'vetra-theme');
    assert.equal(manifest.name, 'Vetra Theme');
    assert.equal(manifest.category, 'theme');
    assert.equal(manifest.api_version, 1);
    assert.equal(manifest.version, '1.0.1');
    assert.deepEqual(manifest.panels, ['admin', 'app', 'server']);
    assert.equal(manifest.version, JSON.parse(read('package.json')).version);
    assert.ok(!('composer_packages' in manifest));
    assert.match(read('src/VetraThemePlugin.php'), /namespace Vetra\\Theme;/);
    assert.ok(!('meta' in manifest));
    assert.equal(manifest.update_url, 'https://hub.pelican.dev/plugins/vetra-theme/update.json');
});
test('Plugin identifier, PHP namespace, routes and environment use the new theme identity', () => {
    assert.equal(manifest.id, 'vetra-theme');
    assert.equal(manifest.namespace, 'Vetra\\Theme');
    assert.equal(manifest.class, 'VetraThemePlugin');
    assert.ok(existsSync(new URL('../config/vetra-theme.php', import.meta.url)));
    assert.ok(existsSync(new URL('../src/VetraThemePlugin.php', import.meta.url)));
    const php = read('src/VetraThemePlugin.php');
    assert.match(php, /return 'vetra-theme';/);
    assert.match(php, /VETRA_THEME_/);
    assert.match(read('src/Providers/BackgroundRouteServiceProvider.php'), /\/vetra-theme\/background/);
    assert.match(read('src/BackgroundStorage.php'), /app\/vetra-theme\/background/);
    assert.match(read('config/vetra-theme.php'), /VETRA_THEME_/);
    assert.doesNotMatch(php, /VetraUiPlugin|VETRA_UI_|vetra-ui/);
});
test('Additive integration and protected settings', () => {
    const php = read('src/VetraThemePlugin.php');
    assert.doesNotMatch(php, /->(?:viteTheme|theme)\(/);
    assert.match(php, /Gate::authorize\('update'/);
    assert.match(php, /writeToEnvironment\(\$variables\)/);
    assert.match(read('resources/views/notice.blade.php'), /Notice::render\(\$notice\)/);
    assert.doesNotMatch(read('resources/views/styles.blade.php'), /@vite/);
    assert.match(read('resources/views/styles.blade.php'), /vetra-theme/);
    assert.match(read('src/Notice.php'), /catch \(Throwable\)/);
    assert.doesNotMatch(read('resources/css/vetra.css'), /@import.*(?:tailwind|filament)/);
});
test('Compiled CSS contains the ambient layers and readable terminal', () => {
    assert.ok(existsSync(new URL('../dist/vetra.css', import.meta.url)), 'Run npm run build first');
    const css = read('dist/vetra.css');
    assert.match(css, /#vetra-ambient/);
    assert.match(css, /#terminal/);
    assert.match(css, /\.fi-page-header-main-ctn\s*>\s*header\.fi-header/);
    assert.match(css, /\.fi-ta\s*>\s*\.fi-ta-ctn/);
    assert.match(css, /\.fi-console-page\s+\.fi-header-actions-ctn\s+\.fi-btn-group/);
    assert.match(css, /font-size:\s*0/);
    assert.match(css, /forced-colors/);
    assert.ok(css.length < 18000, 'Theme should remain small');
});
test('Initial release documentation and screenshots are complete', () => {
    for (const path of [
        'docs/INSTALLATION.md', 'docs/CONFIGURATION.md', 'docs/SCREENSHOTS.md',
        'docs/ARCHITECTURE.md', 'docs/SECURITY.md', 'docs/COMPATIBILITY.md',
        'docs/VALIDATION.md', 'docs/TROUBLESHOOTING.md', 'docs/RELEASE.md',
        'docs/PELICAN_HUB.md', 'CONTRIBUTING.md', 'SECURITY.md',
        'docs/images/admin-eggs.png', 'docs/images/settings-appearance.png',
        'docs/images/settings-notice.png', 'docs/images/server-console.png',
        'docs/images/file-manager.png',
    ]) {
        assert.ok(existsSync(new URL(`../${path}`, import.meta.url)), `Missing ${path}`);
    }
    const publicDocs = read('README.md') + read('CHANGELOG.md');
    assert.doesNotMatch(publicDocs, /0\.2\.[0-9]|0\.1\.0|RECOVERY\.md/i);
    assert.doesNotMatch(publicDocs, /Vetra UI|VetraUI/);
});
test('Hub-blocked integration harness stays out of source and CI', () => {
    assert.equal(existsSync(new URL('../tests/integration.php', import.meta.url)), false);
    const workflow = read('.github/workflows/validate.yml');
    assert.doesNotMatch(workflow, /tests\/integration\.php|Check locked Pelican integration/);
});
