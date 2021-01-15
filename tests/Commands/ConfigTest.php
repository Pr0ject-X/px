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
        ))->execute([]);

        $this->assertEquals(0, $commandStatus);
    }
}
