<?php

declare(strict_types=1);

namespace Vetra\Theme;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Throwable;

final class Notice
{
    public static function render(string $markdown): HtmlString
    {
        $markdown = Appearance::normalize(['notice' => $markdown])['notice'];
        try {
            $html = Str::markdown($markdown, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 20,
                'renderer' => ['soft_break' => "<br>\n"],
            ]);
            // Explicit formatting allowlist. Images, embeds, styles and event handlers
            // do not belong in a global notice and never reach the page.
            $config = (new HtmlSanitizerConfig())->allowRelativeLinks()
                ->allowLinkSchemes(['https', 'http', 'mailto'])->withMaxInputLength(50000);
            foreach (['p', 'br', 'strong', 'em', 'del', 's', 'ul', 'li', 'blockquote', 'pre', 'code', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr'] as $element) {
                $config = $config->allowElement($element);
            }
            $config = $config->allowElement('a', ['href', 'title'])->allowElement('ol', ['start']);

            return new HtmlString((new HtmlSanitizer($config))->sanitize($html));
        } catch (Throwable) {
            // A global notice must never make the whole panel unavailable.
            return new HtmlString(nl2br(e($markdown), false));
        }
    }
}
