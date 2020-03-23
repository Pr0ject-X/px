<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Mocks\Plugins;

use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;

class BasicPlugin extends PluginTasksBase
{
    /**
     * @inheritDoc
     */
    public static function pluginId(): string
    {
        return 'basic';
    }

    /**
     * @inheritDoc
     */
    public static function pluginLabel(): string
    {
        return 'Basic Plugin';
    }
}
