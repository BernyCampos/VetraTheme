<?php

declare(strict_types=1);

namespace Vetra\Theme;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/** Small SVG allowlist for public background images; never resolves entities or networks. */
final class SvgSanitizer
{
    private const ELEMENTS = [
        'svg', 'g', 'defs', 'title', 'desc',
        'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'linearGradient', 'radialGradient', 'stop', 'clipPath', 'mask',
    ];

    private const ATTRIBUTES = [
        'id', 'role', 'aria-label', 'version', 'xmlns', 'xml:space',
        'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry',
        'width', 'height', 'viewBox', 'preserveAspectRatio', 'd', 'points',
        'fill', 'fill-opacity', 'fill-rule', 'stroke', 'stroke-width',
        'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-dasharray',
        'stroke-dashoffset', 'stroke-opacity', 'opacity', 'transform', 'vector-effect',
        'clip-path', 'mask', 'offset', 'stop-color', 'stop-opacity',
        'gradientUnits', 'gradientTransform', 'spreadMethod', 'text-anchor',
        'font-family', 'font-size', 'font-weight', 'dominant-baseline',
    ];

    public static function sanitize(string $dirty): string|false
    {
        if (stripos($dirty, '<!DOCTYPE') !== false || stripos($dirty, '<!ENTITY') !== false) {
            return false;
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument('1.0', 'UTF-8');
        try {
            if (!$document->loadXML($dirty, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_NOERROR | LIBXML_NOWARNING)) {
                return false;
            }
            $root = $document->documentElement;
            if (!$root instanceof DOMElement || $root->localName !== 'svg') {
                return false;
            }

            self::removeProcessingInstructions($document);
            $xpath = new DOMXPath($document);
            $nodes = [];
            foreach ($xpath->query('//*') ?: [] as $node) {
                $nodes[] = $node;
            }
            foreach ($nodes as $element) {
                if (!$element instanceof DOMElement || !$element->parentNode && $element !== $root) {
                    continue;
                }
                if (!in_array($element->localName, self::ELEMENTS, true)) {
                    $element->parentNode?->removeChild($element);
                    continue;
                }
                self::cleanAttributes($element, $element === $root);
            }

            if (!$root->hasAttribute('xmlns')) {
                $root->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
            }
            $safe = $document->saveXML($root);

            return is_string($safe) && preg_match('/<svg(?:\s|>)/', $safe) ? $safe : false;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private static function cleanAttributes(DOMElement $element, bool $isRoot): void
    {
        $attributes = [];
        foreach ($element->attributes as $attribute) {
            $attributes[] = $attribute;
        }
        foreach ($attributes as $attribute) {
            $qualified = $attribute->nodeName;
            $name = $attribute->localName;
            $value = trim($attribute->nodeValue ?? '');
            $allowedName = in_array($qualified, self::ATTRIBUTES, true) || in_array($name, self::ATTRIBUTES, true);
            if (!$allowedName || str_starts_with(strtolower($qualified), 'on') || str_contains(strtolower($qualified), 'href')) {
                $element->removeAttributeNode($attribute);
                continue;
            }
            if ($qualified === 'xmlns' && (!$isRoot || $value !== 'http://www.w3.org/2000/svg')) {
                $element->removeAttributeNode($attribute);
                continue;
            }
            if ($name === 'id' && !preg_match('/\A[A-Za-z_][A-Za-z0-9_.:-]*\z/', $value)) {
                $element->removeAttributeNode($attribute);
                continue;
            }
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (preg_match('/(?:javascript|data|https?|file|ftp)\s*:|\/\/|@import|expression\s*\(/i', $decoded)) {
                $element->removeAttributeNode($attribute);
                continue;
            }
            if (stripos($decoded, 'url(') !== false && !preg_match('/\Aurl\(\s*["\']?#[A-Za-z_][A-Za-z0-9_.:-]*["\']?\s*\)\z/', $decoded)) {
                $element->removeAttributeNode($attribute);
            }
        }
    }

    private static function removeProcessingInstructions(DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }
        foreach ($children as $child) {
            if ($child->nodeType === XML_PI_NODE || $child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                $node->removeChild($child);
                continue;
            }
            self::removeProcessingInstructions($child);
        }
    }
}
