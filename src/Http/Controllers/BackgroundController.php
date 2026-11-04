<?php

declare(strict_types=1);

namespace Vetra\Theme\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Vetra\Theme\BackgroundStorage;

final class BackgroundController
{
    public function __invoke(string $filename, BackgroundStorage $storage): StreamedResponse
    {
        return $storage->response($filename);
    }
}
