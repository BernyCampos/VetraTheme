<?php

declare(strict_types=1);

namespace Vetra\Theme;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

final class BackgroundImage
{
    public const TYPES = [
        'jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp',
        'gif' => 'image/gif', 'avif' => 'image/avif', 'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon', 'svg' => 'image/svg+xml',
    ];

    /** Validate actual contents, preserve raster/animated bytes, sanitize vectors. */
    public static function read(UploadedFile $file): array
    {
        if (!$file->isValid() || $file->getSize() > 5 * 1024 * 1024) {
            self::invalid();
        }
        $bytes = $file->get();
        if (!is_string($bytes) || $bytes === '') {
            self::invalid();
        }
        if (strtolower($file->getClientOriginalExtension()) === 'svg' || $file->getMimeType() === 'image/svg+xml') {
            if (stripos($bytes, '<!DOCTYPE') !== false || stripos($bytes, '<!ENTITY') !== false) {
                self::invalid();
            }
            $safe = SvgSanitizer::sanitize($bytes);
            if (!$safe || !preg_match('/<svg(?:\s|>)/', $safe)) {
                self::invalid();
            }
            return [$safe, 'svg'];
        }
        $info = @getimagesizefromstring($bytes);
        $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif', IMAGETYPE_AVIF => 'avif', IMAGETYPE_BMP => 'bmp', IMAGETYPE_ICO => 'ico'];
        if (!$info || !isset($extensions[$info[2]]) || $info[0] < 1 || $info[1] < 1 || $info[0] > 8192 || $info[1] > 8192) {
            self::invalid();
        }

        return [$bytes, $extensions[$info[2]]];
    }

    private static function invalid(): never
    {
        throw ValidationException::withMessages(['background_upload' => trans('vetra-theme::ui.upload_image_error')]);
    }
}
