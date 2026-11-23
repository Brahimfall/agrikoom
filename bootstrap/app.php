<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Auto-purge stale route cache on deployment if routes/web.php was updated
$bootstrapCacheDir = __DIR__.'/cache';
if (file_exists($routeCache = $bootstrapCacheDir.'/routes-v7.php')) {
    $webRoutes = __DIR__.'/../routes/web.php';
    if (! file_exists($webRoutes) || filemtime($webRoutes) > filemtime($routeCache) || strpos(file_get_contents($routeCache), 'admin.categories') === false) {
        @unlink($routeCache);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
