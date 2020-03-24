<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Commands;

use Pr0jectX\Px\Commands\Config;
use Pr0jectX\Px\Tests\TestCaseCommandBase;
use Symfony\Component\Yaml\Yaml;

/**
 * Define the test case for the config command.
 */
class ConfigTest extends TestCaseCommandBase
{
    /**
     * {@inheritDoc}
     */
    protected function commandName(): string
    {
        return 'config:set';
    }

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

        $this->command->execute([
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
        $this->assertEquals($expectedConfig, $configFile);
        $this->assertEquals(0, $this->command->getStatusCode());
    }
}
