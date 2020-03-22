<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Pr0jectX\Px\PxApp;

abstract class TestCaseBase extends TestCase
{
    /** @var \Pr0jectX\Px\PxApp  */
    protected $app;

    protected $projectFilesystem;

    public function setUp() : void
    {
        $this->setupProjectFilesystem();
        $this->app = new PxApp();
        PxApp::setProjectSearchPath(
            vfsStream::url('root/var/www/html')
        );
        PxApp::createContainer(
            null, null, $this->app, $this->defineClassAutoloader()
        );
    }

    protected function setupProjectFilesystem()
    {
        vfsStream::enableDotfiles();
        $this->projectFilesystem = vfsStream::setup('root', null, [
            'home' => [
                'jackie' => []
            ],
            'var' => [
                'www' => [
                    'html' => [
                        'web' => [],
                        'composer.json' => file_get_contents(
                            __DIR__ . '/fixtures/composer.basic.json'
                        )
                    ]
                ]
            ]
        ]);
    }

    protected function defineClassAutoloader()
    {
        return require(
            dirname(__DIR__) . '/vendor/autoload.php'
        );
    }
}
