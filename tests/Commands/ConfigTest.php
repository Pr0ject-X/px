<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Commands;

use Pr0jectX\Px\Commands\Config;
use Pr0jectX\Px\Tests\TestCaseCommandBase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

/**
 * Define the test case for the config command.
 */
class ConfigTest extends TestCaseCommandBase
{
    /**
     * {@inheritDoc}
     */
    protected function commandClasses(): array
    {
        return [Config::class];
    }

    public function testConfigSet(): void
    {
        $this->setCommandInputs(['localhost']);

        $commandStatus = (new CommandTester(
            $this->app->find('config:set')
        ))->execute([
            'name' => 'environment'
        ]);

        $configFile = Yaml::parseFile(
            'vfs://root/var/www/html/project-x.yml'
        );
        $expectedConfig = [
            'plugins' => [
                'environment' => [
                    'type' => 'localhost'
                ]
            ]
        ];
        $this->assertEquals(0, $commandStatus);
        $this->assertEquals($expectedConfig, $configFile);
    }
}
