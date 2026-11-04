<?php

declare(strict_types=1);

namespace Vetra\Theme;

use App\Contracts\Plugins\HasPluginSettings;
use App\Filament\Server\Pages\Console;
use App\Models\Plugin as PluginModel;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Throwable;

final class VetraThemePlugin implements Plugin, HasPluginSettings
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'vetra-theme';
    }

    public function register(Panel $panel): void
    {
        $settings = Appearance::normalize(config('vetra-theme', []));
        // Additive stylesheet: deliberately never call theme() or viteTheme().
        $panel->renderHook(PanelsRenderHook::STYLES_AFTER, fn () => view('vetra-theme::styles', [
            'css' => Appearance::css($settings),
            'stylesheet' => self::stylesheet(),
        ]));
        $panel->renderHook(PanelsRenderHook::BODY_START, fn () => view('vetra-theme::ambient', [
            'settings' => $settings,
            'panelId' => $panel->getId(),
        ]));
        $panel->renderHook(PanelsRenderHook::CONTENT_START, fn () => view('vetra-theme::notice', [
            'notice' => $settings['notice'],
        ]));
        if ($panel->getId() === 'server') {
            $panel->renderHook(PanelsRenderHook::PAGE_HEADER_HEADING_AFTER,
                fn () => view('vetra-theme::server-context', ['server' => Filament::getTenant()]),
                scopes: [Console::class],
            );
        }
        $panel->colors(array_combine(
            ['primary', 'success', 'warning', 'danger', 'info'],
            array_map(static fn (string $key) => Color::hex($settings[$key]), ['primary', 'success', 'warning', 'danger', 'info']),
        ));
        $panel->defaultThemeMode(ThemeMode::from($settings['mode']));
        $panel->sidebarWidth($settings['sidebar_width'] . 'px');

        // App has no native sidebar. Do not invent empty navigation there.
        if ($panel->getId() !== 'app' && $settings['navigation'] !== 'native') {
            $panel->topNavigation($settings['navigation'] === 'topbar');
            $panel->topbar($settings['navigation'] !== 'sidebar');
        }
    }

    public function boot(Panel $panel): void {}

    private static function stylesheet(): string
    {
        static $stylesheet;
        if (is_string($stylesheet)) {
            return $stylesheet;
        }
        $path = plugin_path('vetra-theme', 'dist/vetra.css');
        $contents = is_readable($path) ? @file_get_contents($path) : false;

        return $stylesheet = is_string($contents) ? $contents : '';
    }

    public function getSettingsFormData(): array
    {
        return Appearance::normalize(config('vetra-theme', [])) + [
            'restore_defaults' => false, 'background_upload' => null, 'remove_background' => false,
        ];
    }

    public function getSettingsForm(): array
    {
        return SettingsForm::schema();
    }

    public function saveSettings(array $data): void
    {
        // Defense in depth: the native plugin action also checks this policy.
        Gate::authorize('update', PluginModel::findOrFail($this->getId()));
        $uploadedPath = null;
        if (filter_var($data['restore_defaults'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $settings = Appearance::DEFAULTS;
        } else {
            Validator::make($data, SettingsForm::rules())->validate();
            $settings = Appearance::normalize($data);
            if (filter_var($data['remove_background'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $settings['background'] = '';
            } elseif ($upload = ($data['background_upload'] ?? null)) {
                // Save only after authorization and full validation. No original filenames.
                $uploadedPath = app(BackgroundStorage::class)->store($upload);
                $settings['background'] = $uploadedPath;
            }
        }

        $variables = [];
        foreach ($settings as $key => $value) {
            if ($key === 'notice') {
                continue;
            }
            $variables['VETRA_THEME_' . strtoupper($key)] = $value;
        }
        $variables['VETRA_THEME_NOTICE_MARKDOWN'] = base64_encode($settings['notice']);

        // Pelican's documented persistence mechanism; no custom filesystem writer.
        try {
            $this->writeToEnvironment($variables);
        } catch (Throwable $exception) {
            if ($uploadedPath !== null) {
                app(BackgroundStorage::class)->discard($uploadedPath);
            }
            throw $exception;
        }
        config(['vetra-theme' => $settings]);
        Notification::make()
            ->title(trans('vetra-theme::ui.saved'))
            ->body(trans('vetra-theme::ui.reload'))
            ->success()
            ->send();
    }
}
