<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/Appearance.php';

use Vetra\Theme\Appearance;

$checks = 0;
$check = static function (bool $ok, string $message) use (&$checks): void {
    if (!$ok) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$check(Appearance::normalize([]) === Appearance::DEFAULTS, 'Defaults must be complete and deterministic');
foreach (['https://evil.test/a.png', '//evil.test/a.jpg', '/a/../b.png', '/a.png?x=1', '/a.svg', '/a".png', '/a%22.png', '/x.png);}</style><script>alert(1)</script>', ['x']] as $url) {
    $check(Appearance::background($url) === '', 'Unsafe image path accepted');
}
$check(Appearance::background('/storage/backgrounds/coast.webp') === '/storage/backgrounds/coast.webp', 'Local image rejected');
$s = Appearance::normalize(['opacity' => -50, 'blur' => 900, 'radius' => 'oops', 'primary' => 'red;display:none', 'style' => 'unsafe', 'reduce_motion' => 'false', 'unknown' => 'bad']);
$check($s['opacity'] === 65 && $s['blur'] === 28, 'Numeric boundaries');
$check($s['radius'] === 16 && $s['primary'] === '#739fe3' && $s['style'] === 'glass', 'Invalid values fallback');
$check($s['reduce_motion'] === false && !array_key_exists('unknown', $s), 'Boolean and key allowlist');
$check(str_contains(Appearance::css(['style' => 'solid']), '--vetra-opacity:1;--vetra-blur:0px'), 'Solid must be opaque without blur');
$check(!str_contains(Appearance::css(['notice' => '</style><script>x</script>']), '<'), 'Notice must never enter CSS');
$check(mb_strlen(Appearance::normalize(['notice' => str_repeat('á', 2100)])['notice']) === 2000, 'UTF-8 length bound');
$check(Appearance::normalize(['notice' => "line1\r\nline2\x00"])['notice'] === "line1\nline2", 'Markdown line breaks preserved, controls removed');
$en = require dirname(__DIR__) . '/lang/en/ui.php';
$es = require dirname(__DIR__) . '/lang/es/ui.php';
$check(array_keys($en) === array_keys($es), 'Translations must have matching keys');
echo "PASS: $checks appearance/security checks\n";
