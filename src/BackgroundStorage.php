<?php

declare(strict_types=1);

namespace Vetra\Theme;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class BackgroundStorage
{
    private FilesystemAdapter $pluginMirror;
    private FilesystemAdapter $persistent;

    public function __construct(?string $pluginPath = null, ?string $persistentPath = null)
    {
        $this->pluginMirror = Storage::build(['driver' => 'local', 'root' => $pluginPath ?? plugin_path('vetra-theme', 'background'), 'throw' => true, 'lazy_root_creation' => true]);
        $this->persistent = Storage::build(['driver' => 'local', 'root' => $persistentPath ?? storage_path('app/vetra-theme/background'), 'throw' => true, 'lazy_root_creation' => true]);
    }

    public function store(UploadedFile $file): string
    {
        [$bytes, $extension] = BackgroundImage::read($file);
        $filename = bin2hex(random_bytes(20)) . '.' . $extension;
        // The durable copy is authoritative because plugin directories may be
        // read-only or replaced during an update.
        try {
            $this->persistent->put($filename, $bytes);
        } catch (Throwable $exception) {
            $this->discard($filename);
            report($exception);
            throw ValidationException::withMessages(['background_upload' => trans('vetra-theme::ui.upload_storage_error')]);
        }

        try {
            // Maintain a plugin/background mirror when deployment permissions
            // allow it. Never chmod or mutate panel permissions.
            $this->pluginMirror->put($filename, $bytes);
        } catch (Throwable $exception) {
            Log::warning('Vetra Theme could not mirror a background into the plugin directory; using the durable panel storage copy.', [
                'exception' => $exception,
            ]);
        }

        return '/vetra-theme/background/' . $filename;
    }

    public function discard(string $path): void
    {
        $filename = basename($path);
        if (!self::validFilename($filename)) {
            return;
        }
        foreach ([$this->persistent, $this->pluginMirror] as $disk) {
            try {
                $disk->delete($filename);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }

    public function response(string $filename): StreamedResponse
    {
        abort_unless(self::validFilename($filename), 404);
        $disk = $this->persistent->exists($filename) ? $this->persistent : $this->pluginMirror;
        abort_unless($disk->exists($filename), 404);

        return $disk->response($filename, $filename, [
            'Content-Type' => BackgroundImage::TYPES[pathinfo($filename, PATHINFO_EXTENSION)],
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ], 'inline');
    }

    public static function validFilename(string $filename): bool
    {
        return (bool) preg_match('/\A[a-f0-9]{40}\.(?:jpg|png|webp|gif|avif|bmp|ico|svg)\z/', $filename);
    }
}
