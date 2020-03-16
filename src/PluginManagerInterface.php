<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Robo\ClassDiscovery\ClassDiscoveryInterface;

/**
 * Define the plugin manager interface.
 */
interface PluginManagerInterface
{
    /**
     * Get plugin options.
     *
     * @return array
     *   An array of the available plugins.
     */
    public function getOptions() : array;

    /**
     * Get plugin class name.
     *
     * @param string $plugin_id
     *   The plugin identifier.
     *
     * @return bool|string
     *   Return the plugin class name; otherwise false.
     */
    public function getClassname(string $plugin_id);

    /**
     * Discover the plugin classes.
     *
     * @param \Robo\ClassDiscovery\ClassDiscoveryInterface $classDiscovery
     *
     * @return array
     *   An array of plugin classnames.
     */
    public function discover(ClassDiscoveryInterface $classDiscovery) : array;

    /**
     * Create the plugin instance.
     *
     * @param string $name
     *   The plugin machine name.
     * @param array $configurations
     *   The plugin configurations.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\PluginInterface The instantiated plugin class.
     *   The instantiated plugin class.
     */
    public function createInstance(string $name, array $configurations) : PluginInterface;

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
    public function loadInstancesWithInterface(string $interface, array $configurations = []) : array;
}
