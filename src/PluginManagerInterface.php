<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;

/**
 * Define the plugin manager interface.
 */
interface PluginManagerInterface
{
    /**
     * Discover the plugin classes.
     *
     * @return array
     *   An array of plugin classnames.
     */
    public function discover(): array;

    /**
     * Get plugin options.
     *
     * @return array
     *   An array of the available plugins.
     */
    public function getOptions(): array;

    /**
     * Get plugin class name.
     *
     * @param string $pluginId
     *   The plugin identifier.
     *
     * @return string
     *   Return the plugin class name; otherwise an empty string.
     */
    public function getClassname(string $pluginId);

    /**
     * Create the plugin instance.
     *
     * @param string $pluginId
     *   The plugin identifier.
     * @param array $configurations
     *   The plugin configurations.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\PluginInterface
     *   The instantiated plugin class.
     */
    public function createInstance(string $pluginId, array $configurations): PluginInterface;

    /**
     * Load plugin instance with interface.
     *
     * @param string $interface
     *   The fully qualified interface name.
     * @param array $configurations
     *   An array of plugin configurations keyed by the plugin id.
     *
     * @return array
     *   An array of plugin instances that match the defined interface.
     */
    public function loadInstancesWithInterface(string $interface, array $configurations = []): array;
}
