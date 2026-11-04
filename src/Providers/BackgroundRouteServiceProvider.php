<?php

declare(strict_types=1);

namespace Vetra\Theme\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Throwable;
use Vetra\Theme\Http\Controllers\BackgroundController;

final class BackgroundRouteServiceProvider extends RouteServiceProvider
{
    public function boot(): void
    {
        try {
            $this->routes(function (): void {
                Route::get('/vetra-theme/background/{filename}', BackgroundController::class)
                    ->where('filename', '[a-f0-9]{40}\.(?:jpg|png|webp|gif|avif|bmp|ico|svg)')
                    ->name('vetra-theme.background');
            });
        } catch (Throwable) {
            // Route registration may fail when a stale route cache is present.
            // The theme itself must still boot; only the optional image is absent.
        }
    }
}
