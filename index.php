<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-purge stale route cache on OVH if routes have been modified
if (file_exists($routeCache = __DIR__.'/bootstrap/cache/routes-v7.php')) {
    $webRoutes = __DIR__.'/routes/web.php';
    if (! file_exists($webRoutes) || filemtime($webRoutes) > filemtime($routeCache) || strpos(file_get_contents($routeCache), 'admin.categories') === false) {
        @unlink($routeCache);
    }
}

// Determine if the application is in maintenance mode...
// NOTE: This index.php is at www/ root on OVH (no public/ subfolder needed)
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());

