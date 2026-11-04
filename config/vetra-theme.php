<?php

use Vetra\Theme\Appearance;

$settings = [];
foreach (Appearance::DEFAULTS as $key => $default) {
    $settings[$key] = env('VETRA_THEME_' . strtoupper($key), $default);
}

// Keep multiline Markdown in one environment line.
$encodedNotice = env('VETRA_THEME_NOTICE_MARKDOWN');
if (is_string($encodedNotice)) {
    $decodedNotice = base64_decode($encodedNotice, true);
    $settings['notice'] = $decodedNotice === false ? '' : $decodedNotice;
}

return $settings;
