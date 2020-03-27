<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
use Pr0jectX\Px\PxApp;
use Robo\Config\Config;
use Symfony\Component\Console\Input\InputInterface;

class PxAppTest extends TestCaseBase
{
    public function testDisplayBanner(): void
    {
        $this->assertNotEmpty(
            PxApp::displayBanner()
        );
    }

    public function testDisplayVersion(): void
    {
        $this->assertRegExp(
            "/\d+\.\d+.\d+/",
            PxApp::displayVersion()
        );
    }

    public function testService(): void
    {
        $this->assertInstanceOf(
            InputInterface::class,
            PxApp::service('input')
        );

        $this->expectException(NotFoundException::class);
        PxApp::service('notfound');
    }

    public function testGetContainer(): void
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            PxApp::getContainer()
        );
    }

    public function testHasContainer(): void
    {
        $this->assertTrue(
            PxApp::hasContainer()
        );
    }

    public function testUserDir(): void
    {
        $this->assertEquals(
            'vfs://root/home/jackie',
            PxApp::userDir()
        );
    }

    public function testUserShell(): void
    {
        $this->assertEquals('zsh', PxApp::userShell());
    }

    public function testGlobalTempDir(): void
    {
        $this->assertEquals(
            'vfs://root/home/jackie/.project-x',
            PxApp::globalTempDir()
        );
    }

    public function testProjectTempDir(): void
    {
        $this->assertEquals(
            'vfs://root/var/www/html/.project-x',
            PxApp::projectTempDir()
        );
    }

    public function testProjectRootPath(): void
    {
        $this->assertEquals(
            'vfs://root/var/www/html',
            PxApp::projectRootPath()
        );

        PxApp::setProjectSearchPath(
            'vfs://root/var/www/html/web'
        );

        $this->assertEquals(
            'vfs://root/var/www/html',
            PxApp::projectRootPath()
        );
    }

    public function testGetEnvironmentType(): void
    {
        $this->assertEquals(
            'localhost',
            PxApp::getEnvironmentType()
        );
    }

    public function testGetEnvironmentInstance(): void
    {
        $this->assertInstanceOf(
            EnvironmentTypeInterface::class,
            PxApp::getEnvironmentInstance()
        );
    }

    public function testGetConfiguration(): void
    {
        $this->assertInstanceOf(
            Config::class,
            PxApp::getConfiguration()
        );
    }

    public function testGetProjectComposer(): void
    {
        PxApp::loadProjectComposer();
        $this->assertNotEmpty(
            PxApp::getProjectComposer()
        );
    }

    public function testComposerHasPackage(): void
    {
        PxApp::loadProjectComposer();
        $this->assertFalse(
            PxApp::composerHasPackage('symfony/process')
        );
        $this->assertTrue(
            PxApp::composerHasPackage('consolidation/robo')
        );
    }
}
