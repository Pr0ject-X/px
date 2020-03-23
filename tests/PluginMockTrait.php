<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\Tests\Mocks\Plugins\BasicPlugin;
use Pr0jectX\Px\Tests\Mocks\Plugins\CommandPlugin;
use Pr0jectX\Px\Tests\Mocks\Plugins\StandardPlugin;

/**
 * Define the plugin mock common methods.
 */
trait PluginMockTrait
{
    protected function getMockPlugins()
    {
        return [
            BasicPlugin::class,
            CommandPlugin::class,
            StandardPlugin::class
        ];
    }
}
