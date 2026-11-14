<?php

declare(strict_types=1);

namespace Vetra\Theme;

use App\Contracts\Plugins\HasPluginSettings;
use App\Contracts\Plugins\HasTheme;
use App\Filament\Server\Pages\Console;
use App\Models\Plugin as PluginModel;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Throwable;

final class VetraThemePlugin implements Plugin, HasTheme, HasPluginSettings
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'vetra-theme';
    }

    public function register(Panel $panel): void
    {
        // Never change the shared Filament layout. Pelican's ThemeService
        // applies theme entries, colors and hooks only to the active theme.
    }

    public function boot(Panel $panel): void {}

    public function getThemeViteEntry(): string|array|null
    {
        return 'plugins/vetra-theme/resources/css/vetra.css';
    }

    public function getThemeColors(): array
    {
        $settings = Appearance::normalize(config('vetra-theme', []));

        return [
            'primary' => Color::hex($settings['primary']),
            'success' => Color::hex($settings['success']),
            'warning' => Color::hex($settings['warning']),
            'danger' => Color::hex($settings['danger']),
            'info' => Color::hex($settings['info']),
        ];
    }

    public function getThemeFont(): ?string
    {
        return null;
    }

    public function getThemeRenderHooks(): array
    {
        return [
            PanelsRenderHook::STYLES_AFTER => static fn (): string => view('vetra-theme::styles', [
                'css' => Appearance::css(Appearance::normalize(config('vetra-theme', []))),
            ])->render(),

            PanelsRenderHook::BODY_START => static fn (): string => view('vetra-theme::ambient', [
                'settings' => Appearance::normalize(config('vetra-theme', [])),
                'panelId' => Filament::getCurrentPanel()?->getId() ?? 'app',
            ])->render(),

            PanelsRenderHook::CONTENT_START => static fn (): string => view('vetra-theme::notice', [
                'notice' => Appearance::normalize(config('vetra-theme', []))['notice'],
            ])->render(),

            PanelsRenderHook::PAGE_HEADER_HEADING_AFTER => static function (array $scopes = []): string {
                // Never display server-specific details on unrelated page headers.
                if (Filament::getCurrentPanel()?->getId() !== 'server'
                    || !in_array(Console::class, $scopes, true)) {
                    return '';
                }

                return view('vetra-theme::server-context', [
                    'server' => Filament::getTenant(),
                ])->render();
            },
        ];
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
            $settings = Appearance::normalize(array_replace(config('vetra-theme', []), $data));
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
