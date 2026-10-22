<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../backend/vendor/autoload.php';

$storagePath = sys_get_temp_dir().'/prepracticas';

foreach (['framework/views', 'framework/cache/data', 'framework/sessions', 'logs'] as $directory) {
    $path = $storagePath.'/'.$directory;

    if (! is_dir($path) && ! mkdir($path, 0755, true) && ! is_dir($path)) {
        throw new RuntimeException('No se pudo preparar el almacenamiento temporal.');
    }
}

/** @var Application $app */
$app = require __DIR__.'/../backend/bootstrap/app.php';
$app->useStoragePath($storagePath);
$app->handleRequest(Request::capture());
