<?php
$rootDir = dirname(__DIR__);
require $rootDir . '/vendor/autoload.php';

spl_autoload_register(function($class) {
    $baseDir = dirname(__DIR__) . '/src';
    $offset = strlen('Haoa\\JsonRpc\\');
    $path = substr($class, $offset, strlen($class));
    $path = $baseDir . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $path) . '.php';
    var_dump($path);
    require($path);
});