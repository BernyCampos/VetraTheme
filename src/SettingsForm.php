<?php

declare(strict_types=1);

namespace Vetra\Theme;

use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;

final class SettingsForm
{
    public static function rules(): array
    {
        $rules = [
            'style' => 'required|in:glass,solid,floating',
            'mode' => 'required|in:dark,light,system',
            'navigation' => 'required|in:native,sidebar,topbar,mixed',
            'density' => 'required|in:comfortable,compact',
            'reduce_motion' => 'required|boolean',
            'side_charts' => 'required|boolean',
            'notice' => 'nullable|string|max:2000',
            'background' => ['nullable', 'string', 'max:200', self::backgroundRule()],
            'background_upload' => ['bail', 'nullable', 'file', 'max:5120', self::uploadRule()],
            'remove_background' => ['sometimes', 'boolean'],
        ];
        foreach (array_keys(Appearance::COLORS) as $key) {
            $rules[$key] = ['required', 'regex:/\A#[0-9a-fA-F]{6}\z/'];
        }
        foreach (['opacity' => [65, 100], 'blur' => [0, 28], 'background_blur' => [0, 24], 'background_dim' => [25, 85], 'radius' => [4, 24], 'sidebar_width' => [220, 300]] as $key => [$min, $max]) {
            $rules[$key] = "required|integer|min:$min|max:$max";
        }

        return $rules;
    }

    public static function backgroundRule(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            if ($value !== null && $value !== '' && Appearance::background($value) === '') {
                $fail(trans('vetra-theme::ui.background_error'));
            }
        };
    }

    public static function uploadRule(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                $fail(trans('vetra-theme::ui.upload_image_error'));
                return;
            }
            try {
                BackgroundImage::read($value);
            } catch (\Illuminate\Validation\ValidationException) {
                $fail(trans('vetra-theme::ui.upload_image_error'));
            }
        };
    }

    public static function schema(): array
    {
        $select = static fn (string $key, array $options) => Select::make($key)
            ->label(trans("vetra-theme::ui.$key"))->options(array_combine($options, array_map(static fn ($v) => trans("vetra-theme::ui.options.$v"), $options)))
            ->required()->live();
        $number = static fn (string $key, int $min, int $max) => TextInput::make($key)
            ->label(trans("vetra-theme::ui.$key"))->numeric()->integer()->minValue($min)->maxValue($max)->required()->live(onBlur: true);

        return [
            Section::make(trans('vetra-theme::ui.appearance'))->columns(2)->schema([
                $select('style', ['glass', 'solid', 'floating']),
                $select('mode', ['dark', 'light', 'system'])->helperText(trans('vetra-theme::ui.mode_help')),
                $select('navigation', ['native', 'sidebar', 'topbar', 'mixed']),
                $select('density', ['comfortable', 'compact']),
                ColorPicker::make('primary')->label(trans('vetra-theme::ui.primary'))->hex()->required()->live(onBlur: true),
                ColorPicker::make('secondary')->label(trans('vetra-theme::ui.secondary'))->hex()->required()->live(onBlur: true),
                ColorPicker::make('accent')->label(trans('vetra-theme::ui.accent'))->hex()->required()->live(onBlur: true),
                $number('radius', 4, 24),
                $number('sidebar_width', 220, 300),
                Toggle::make('side_charts')->label(trans('vetra-theme::ui.side_charts')),
                Toggle::make('reduce_motion')->label(trans('vetra-theme::ui.reduce_motion')),
            ]),
            Section::make(trans('vetra-theme::ui.material'))->columns(2)->schema([
                $number('opacity', 65, 100),
                $number('blur', 0, 28),
                FileUpload::make('background_upload')
                    ->label(trans('vetra-theme::ui.background_upload'))
                    ->disk('public')->visibility('public')->storeFiles(false)
                    ->preventFilePathTampering()
                    ->acceptedFileTypes([...array_values(BackgroundImage::TYPES), 'image/x-icon', 'image/x-ms-bmp'])
                    ->maxSize(5120)->rules([fn (): Closure => self::uploadRule()])
                    ->helperText(trans('vetra-theme::ui.background_upload_help'))->columnSpanFull(),
                View::make('vetra-theme::background-preview')->viewData(fn (Get $get) => [
                    'background' => Appearance::background($get('background')),
                ])->columnSpanFull(),
                Toggle::make('remove_background')->label(trans('vetra-theme::ui.remove_background'))
                    ->helperText(trans('vetra-theme::ui.remove_background_help'))->columnSpanFull(),
                TextInput::make('background')->label(trans('vetra-theme::ui.background'))->maxLength(200)
                    ->rules([fn (): Closure => self::backgroundRule()])->helperText(trans('vetra-theme::ui.background_help'))->columnSpanFull()->live(onBlur: true),
                $number('background_blur', 0, 24),
                $number('background_dim', 25, 85),
                View::make('vetra-theme::preview')->viewData(fn (Get $get) => [
                    'settings' => Appearance::normalize(array_combine(array_keys(Appearance::DEFAULTS), array_map(fn ($key) => $get($key), array_keys(Appearance::DEFAULTS)))),
                ])->columnSpanFull(),
            ]),
            Section::make(trans('vetra-theme::ui.palette'))->description(trans('vetra-theme::ui.palette_help'))
                ->collapsed()->schema([
                    Section::make(trans('vetra-theme::ui.status_colors'))->columns(2)->schema(array_map(
                        static fn (string $key) => ColorPicker::make($key)->label(trans("vetra-theme::ui.colors.$key"))->hex()->required(),
                        ['success', 'warning', 'danger', 'info'],
                    )),
                    ...array_map(static fn (string $mode) => Section::make(trans("vetra-theme::ui.options.$mode"))->columns(2)->schema(array_map(
                        static fn (string $key) => ColorPicker::make($mode . '_' . $key)->label(trans("vetra-theme::ui.colors.$key"))->hex()->required(),
                        ['page', 'surface', 'sidebar', 'topbar', 'text', 'muted', 'border', 'notice', 'notice_accent', 'console', 'input'],
                    )), ['dark', 'light']),
                ]),
            Textarea::make('notice')->label(trans('vetra-theme::ui.notice'))->maxLength(2000)->rows(5)
                ->helperText(trans('vetra-theme::ui.notice_help')),
            Toggle::make('restore_defaults')->label(trans('vetra-theme::ui.restore'))->helperText(trans('vetra-theme::ui.restore_help')),
        ];
    }
}
