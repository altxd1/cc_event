<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Laravel - A PHP Framework For Web Artisans
|--------------------------------------------------------------------------
|
| This is the entry point for all HTTP requests into your Laravel
| application. It loads Composer's autoloader, bootstraps the framework by
| requiring the application instance from `bootstrap/app.php`, and then
| passes the incoming request to the HTTP kernel for handling.
|
| Earlier revisions of this project omitted this file, resulting in a
| fatal error when PHP attempted to locate it. Restoring this file
| resolves that issue and brings the project back into alignment with
| Laravel's default directory structure.
|
*/

define('LARAVEL_START', microtime(true));

// Autoload dependencies installed via Composer.
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the application. This file returns the application
// instance after setting up paths, configuration, and service providers.
$app = require_once __DIR__.'/../bootstrap/app.php';

// Resolve the HTTP kernel from the container.
/** @var \Illuminate\Contracts\Http\Kernel $kernel */
$kernel = $app->make(Kernel::class);

// Handle the incoming request and send the response to the browser.
$response = $kernel->handle(
    $request = Request::capture()
)->send();

// Perform any termination logic required by the kernel.
$kernel->terminate($request, $response);