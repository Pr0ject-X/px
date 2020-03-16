<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

/**
 * Define the plugin command register interface.
 */
interface PluginCommandRegisterInterface
{
    /**
     * Register an array of commands to a plugin.
     *
     * @return array
     *   An array of plugin commands.
     */
    public function registeredCommands() : array;
}
