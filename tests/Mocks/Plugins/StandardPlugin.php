<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Mocks\Plugins;

use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;

class StandardPlugin extends PluginTasksBase
{
    /**
     * @inheritDoc
     */
    public static function pluginId(): string
    {
        return 'standard';
    }

    /**
     * @inheritDoc
     */
    public static function pluginLabel(): string
    {
        return 'Standard Plugin';
    }
}
