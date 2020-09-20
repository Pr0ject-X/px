<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Pr0jectX\Px\PxApp;

abstract class TestCaseBase extends TestCase
{
    /**
     * @var \Pr0jectX\Px\PxApp
     */
    protected $app;

    /**
     * @var \org\bovigo\vfs\vfsStreamContent
     */
    protected $projectHomeRoot;

    /**
     * @var \org\bovigo\vfs\vfsStreamContent
     */
    protected $projectHtmlRoot;

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $projectFilesystem;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $this->setupProjectFilesystem();

        $this->projectHomeRoot = $this->projectFilesystem
            ->getChild('home');

        $this->projectHtmlRoot = $this->projectFilesystem
            ->getChild('var')
            ->getChild('www')
            ->getChild('html');

        $this->setBasicComposerJson();
        $this->setProjectXConfiguration();

        $this->app = new PxApp();
        PxApp::setProjectSearchPath(
            $this->projectHtmlRoot->url()
        );
        PxApp::createContainer(
            null,
            null,
            $this->app,
            $this->defineClassAutoloader()
        );
    }

    /**
     * Setup the project filesystem base.
     */
    protected function setupProjectFilesystem(): void
    {
        vfsStream::enableDotfiles();
        $this->projectFilesystem = vfsStream::setup('root', null, [
            'home' => [
                'jackie' => [
                    '.zshrc' => ''
                ]
            ],
            'var' => [
                'www' => [
                    'html' => [
                        'web' => []
                    ]
                ]
            ]
        ]);
    }

    /**
     * Set the application root environment variable.
     */
    protected function setApplicationRoot(): void
    {
        define('APPLICATION_ROOT', dirname(__DIR__, 1));
    }

    /**
     * Set the basic composer json fixture file.
     *
     * @return \org\bovigo\vfs\vfsStreamFile
     */
    protected function setBasicComposerJson(): vfsStreamFile
    {
        return vfsStream::newFile('composer.json', 0775)
            ->setContent(file_get_contents(
                __DIR__ . '/fixtures/composer.basic.json'
            ))
            ->at($this->projectHtmlRoot);
    }

    /**
     * Set project-x configuration.
     *
     * @return \org\bovigo\vfs\vfsStreamFile
     */
    protected function setProjectXConfiguration(): vfsStreamFile
    {
        $contents = $this->buildProjectXConfigContents();

        return vfsStream::newFile('project-x.yml', 0775)
            ->setContent($contents)
            ->at($this->projectHtmlRoot);
    }

    /**
     * Build project-x configuration contents.
     *
     * @return string
     *   The project-x configuration contents.
     */
    protected function buildProjectXConfigContents(): string
    {
        $files = [
            '/fixtures/project-x.hook.yml',
            '/fixtures/project-x.workflow.yml'
        ];
        $contents = [];

        foreach ($files as $filename) {
            $contents[] = file_get_contents(
                __DIR__ . $filename
            );
        }

        return implode("\n", $contents);
    }

    /**
     * Define the class autoloader.
     *
     * @return mixed
     */
    protected function defineClassAutoloader()
    {
        return require(
            dirname(__DIR__) . '/vendor/autoload.php'
        );
    }
}
