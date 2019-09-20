<?php

use Droath\ProjectX\PxApp;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

define('APPLICATION_ROOT', dirname(__DIR__));

// Define possible paths to search for the composer autoloader.
$autoLoaders = [
    '/../../autoload.php',
    '/vendor/autoload.php',
    '/../../vendor/autoload.php',
];
$autoloadPath = false;

foreach ($autoLoaders as $path) {
    if (file_exists(APPLICATION_ROOT . $path)) {
        $autoloadPath = APPLICATION_ROOT . $path;
        break;
    }
}

if (!$autoloadPath) {
    die("Could not find autoloader. Run 'composer install'.");
}
$classLoader = require "$autoloadPath";

$input = new ArgvInput($_SERVER['argv']);
$output = new ConsoleOutput();

$statusCode = (new PxApp($input, $output, $classLoader))
    ->execute();

exit($statusCode);
