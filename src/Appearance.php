<?php

declare(strict_types=1);

namespace Vetra\Theme;

/** Pure, bounded values: never interpolate arbitrary CSS, HTML or URLs. */
final class Appearance
{
    public const COLORS = [
        'primary' => '#739fe3', 'secondary' => '#9a86bc', 'accent' => '#c59a61',
        'success' => '#6ba98a', 'warning' => '#c59a61', 'danger' => '#d87985', 'info' => '#739fe3',
        'light_page' => '#e8edf5', 'light_surface' => '#f7f9fd',
        'light_sidebar' => '#f7f9fd', 'light_topbar' => '#f7f9fd',
        'light_text' => '#233149', 'light_muted' => '#4e5e77', 'light_border' => '#3a4b69',
        'light_notice' => '#f7f9fd', 'light_notice_accent' => '#936d37',
        'light_console' => '#101722', 'light_input' => '#f7f9fd',
        'dark_page' => '#111927', 'dark_surface' => '#1d2639',
        'dark_sidebar' => '#1d2639', 'dark_topbar' => '#1d2639',
        'dark_text' => '#edf1f9', 'dark_muted' => '#b8c2d5', 'dark_border' => '#c9dbff',
        'dark_notice' => '#1d2639', 'dark_notice_accent' => '#c59a61',
        'dark_console' => '#101722', 'dark_input' => '#1d2639',
    ];

    public const DEFAULTS = [
        'style' => 'glass',
        'mode' => 'dark',
        'navigation' => 'mixed',
        ...self::COLORS,
        'opacity' => 82,
        'blur' => 18,
        'background_blur' => 8,
        'background_dim' => 55,
        'background' => '',
        'radius' => 16,
        'density' => 'comfortable',
        'sidebar_width' => 240,
        'reduce_motion' => true,
        'side_charts' => true,
        'notice' => '',
    ];

    public static function normalize(array $input): array
    {
        $out = self::DEFAULTS;
        foreach (['style' => ['glass', 'solid', 'floating'], 'mode' => ['dark', 'light', 'system'], 'navigation' => ['native', 'sidebar', 'topbar', 'mixed'], 'density' => ['comfortable', 'compact']] as $key => $allowed) {
            if (isset($input[$key]) && in_array($input[$key], $allowed, true)) {
                $out[$key] = $input[$key];
            }
        }
        foreach (array_keys(self::COLORS) as $key) {
            if (is_string($input[$key] ?? null) && preg_match('/\A#[0-9a-fA-F]{6}\z/', $input[$key])) {
                $out[$key] = strtolower($input[$key]);
            }
        }
        foreach (['opacity' => [65, 100], 'blur' => [0, 28], 'background_blur' => [0, 24], 'background_dim' => [25, 85], 'radius' => [4, 24], 'sidebar_width' => [220, 300]] as $key => [$min, $max]) {
            if (isset($input[$key]) && is_numeric($input[$key])) {
                $out[$key] = max($min, min($max, (int) $input[$key]));
            }
        }
        foreach (['reduce_motion', 'side_charts'] as $key) {
            $out[$key] = filter_var($input[$key] ?? $out[$key], FILTER_VALIDATE_BOOLEAN);
        }
        $out['background'] = self::background($input['background'] ?? '');
        if (is_string($input['notice'] ?? null)) {
            $notice = str_replace(["\r\n", "\r"], "\n", $input['notice']);
            $out['notice'] = mb_substr(trim(preg_replace('/[\x00-\x08\x0b-\x1f\x7f]/u', ' ', $notice) ?? ''), 0, 2000);
        }

        return $out;
    }

    /** Only same-origin absolute image paths; no remote requests or CSS escapes. */
    public static function background(mixed $value): string
    {
        if (!is_string($value) || strlen($value) > 200 || str_contains($value, '..')) {
            return '';
        }

        // SVG is allowed only through our sanitized image endpoint.
        if (preg_match('~\A/vetra-theme/background/[a-f0-9]{40}\.svg\z~', $value)) {
            return $value;
        }

        return preg_match('~\A/(?!/)[a-zA-Z0-9/_-]+\.(?:avif|webp|png|jpe?g|gif|bmp|ico)\z~i', $value) ? $value : '';
    }

    public static function css(array $input): string
    {
        $s = self::normalize($input);
        $vars = [];
        foreach (array_keys(self::COLORS) as $key) {
            $name = str_replace('_', '-', $key);
            $vars[] = "--vetra-$name:{$s[$key]}";
            if (str_starts_with($key, 'light_') || str_starts_with($key, 'dark_')) {
                $rgb = implode(' ', array_map('hexdec', str_split(substr($s[$key], 1), 2)));
                $vars[] = "--vetra-$name-rgb:$rgb";
            }
        }
        $vars[] = '--vetra-opacity:' . ($s['style'] === 'solid' ? 1 : $s['opacity'] / 100);
        $vars[] = '--vetra-blur:' . ($s['style'] === 'solid' ? 0 : $s['blur']) . 'px';
        $vars[] = '--vetra-background-blur:' . $s['background_blur'] . 'px';
        $vars[] = '--vetra-dim:' . $s['background_dim'] / 100;
        $vars[] = '--vetra-radius:' . $s['radius'] . 'px';
        $vars[] = '--vetra-gap:' . ($s['density'] === 'compact' ? 12 : 20) . 'px';
        $vars[] = '--vetra-image:' . ($s['background'] ? 'url("' . $s['background'] . '")' : 'none');

        return implode(';', $vars) . ';';
    }
}
