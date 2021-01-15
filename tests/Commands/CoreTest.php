<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\Commands\Core;
use Pr0jectX\Px\PxApp;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class CoreTest extends TestCaseCommandBase
{
    use TestDirectoryTrait;

    /**
     * @var string
     */
    protected $testDirectory;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $localFilesystem;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->setTestDirectory(
            dirname(__DIR__, 1) . '/tmp'
        );
        $this->localFilesystem = new Filesystem();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanTestDirectory();
    }

    /**
     * @inheritDoc
     */
    protected function commandClasses(): array
    {
        return [
            Core::class
        ];
    }

    public function testCoreInstall()
    {
        PxApp::loadProjectComposer();
        $this->createTestDirectory();

        $this->setCommandInputs([
            'pr0ject-x/px-drupalvm'
        ]);

        $commandStatus = (new CommandTester(
            $this->app->find('core:install')
        ))->execute([
            'searchTerm' => 'drupalvm', [
                'no-interaction' => true,
                'working-directory' => $this->testDirectory,
            ]
        ]);
        $composerFile = json_decode(
            $this->getTestDirectoryFileContents(
                'composer.json'
            ),
            true
        );
        $composerRequireDev = $composerFile['require-dev'] ?? [];

        $this->assertArrayHasKey(
            'pr0ject-x/px-drupalvm',
            $composerRequireDev
        );
        $this->assertEquals(0, $commandStatus);
        $this->assertCount(1, $composerRequireDev);
        $this->cleanTestDirectory();
    }

    public function testCoreBookmarkSave(): void
    {
        $projectRoot = PxApp::projectRootPath();
        $globalTempDir = PxApp::globalTempDir();

        $commandStatus = (new CommandTester(
            $this->app->find('core:bookmark-save')
        ))->execute([
            'name' => 'Testing'
        ]);

        $projectsJsonContents = json_decode(file_get_contents(
            "{$globalTempDir}/projects.json"
        ), true);

        $expected[$projectRoot] = [
            'name' => 'Testing',
            'path' => $projectRoot
        ];

        $this->assertEquals(0, $commandStatus);
        $this->assertEquals($expected, $projectsJsonContents);
    }

    public function testCoreBookmarkRemove(): void
    {
        $projectRoot = PxApp::projectRootPath();

        $this->setupGlobalProjects([
            $projectRoot => [
                'name' => 'Project Name',
                'path' => $projectRoot
            ]
        ]);
        $globalTempDir = PxApp::globalTempDir();
        $globalTempFile = "{$globalTempDir}/projects.json";

        $expected[$projectRoot] = [
            'name' => 'Project Name',
            'path' => $projectRoot
        ];

        $this->assertEquals($expected, json_decode(
            file_get_contents($globalTempFile),
            true
        ));

        $commandStatus = (new CommandTester(
            $this->app->find('core:bookmark-remove')
        ))->execute([]);

        $this->assertEquals(0, $commandStatus);
        $this->assertEquals("[]", file_get_contents(
            $globalTempFile
        ));
    }

    public function testCoreSwitch(): void
    {
        $this->setupGlobalProjects([
            '/var/www/html/project-one' => [
                'name' => 'Project One',
                'path' => '/var/www/html/project-one'
            ],
            '/var/www/html/project-two' => [
                'name' => 'Project Two',
                'path' => '/var/www/html/project-two'
            ],
            '/var/www/html/project-three' => [
                'name' => 'Project Three',
                'path' => '/var/www/html/project-tree'
            ]
        ]);
        $this->setCommandInputs(['project-two']);

        $command = (new CommandTester(
            $this->app->find('core:switch')
        ));

        $commandStatus = $command->execute(
            [['raw' => true]]
        );

        $this->assertEquals(0, $commandStatus);
    }

    public function testCoreInstallCli(): void
    {
        $this->setApplicationRoot();
        $this->setCommandInputs(['y']);

        $command = new CommandTester(
            $this->app->find('core:install-cli')
        );
        $commandStatus = $command->execute([]);
        $userDir = PxApp::userDir();

        $userShellFile = file_get_contents(
            "{$userDir}/.zshrc"
        );

        $this->assertEquals(0, $commandStatus);
        $this->assertRegExp('/function px\(\)\n{/', $userShellFile);
        $this->assertRegExp('/function px-switch\(\)\n{/', $userShellFile);
    }

    /**
     * Setup the global projects in the temp directory.
     *
     * @param array $projects
     *   An array of the projects.
     *
     * @return false|int
     */
    protected function setupGlobalProjects(array $projects)
    {
        $globalTempDir = PxApp::globalTempDir();

        mkdir($globalTempDir);

        return file_put_contents(
            "{$globalTempDir}/projects.json",
            json_encode($projects)
        );
    }
}
