<?php

use Pr0jectX\Px\PxApp;
use Robo\Robo;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

define('APPLICATION_ROOT', dirname(__DIR__));

// Define possible paths to search for the composer autoloader.
$autoLoaders = [
    '/../../autoload.php',
    '/../../vendor/autoload.php',
    '/vendor/autoload.php',
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

$app = new PxApp();
PxApp::setProjectSearchPath(getcwd());
PxApp::loadProjectComposer();
PxApp::createContainer(
    $input,
    $output,
    $app,
    $classLoader
);
$statusCode = $app->execute();

exit($statusCode);
