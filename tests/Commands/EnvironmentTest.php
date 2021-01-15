<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\Commands\Environment;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

/**
 * Define the test case for the environment command.
 */
class EnvironmentTest extends TestCaseCommandBase
{
    /**
     * {@inheritDoc}
     */
    protected function commandClasses(): array
    {
        return [Environment::class];
    }

    public function testEnvSet(): void
    {
        $this->setCommandInputs(['localhost']);

        $commandStatus = (new CommandTester(
            $this->app->find('env:set')
        ))->execute([]);

        $configFile = Yaml::parseFile(
            'vfs://root/var/www/html/project-x.yml'
        );

        $expectedConfig = [
            'environment' => 'localhost'
        ];
        $this->assertEquals(0, $commandStatus);
        $this->assertArrayNotHasKey('options', $configFile);
        $this->assertEquals($expectedConfig['environment'], $configFile['environment']);
    }
}
