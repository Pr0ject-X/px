<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Mocks\Plugins;

use Pr0jectX\Px\ProjectX\Plugin\PluginCommandRegisterInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;

class CommandPlugin extends PluginTasksBase implements PluginCommandRegisterInterface
{
    /**
     * @inheritDoc
     */
    public static function pluginId(): string
    {
        return 'command';
    }

    /**
     * @inheritDoc
     */
    public static function pluginLabel(): string
    {
        return 'Command Plugin';
    }

    /**
     * @inheritDoc
     */
    public function registeredCommands(): array
    {
        return [];
    }
}
