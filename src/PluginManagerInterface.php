<?php

namespace Pr0jectX\Px;

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
    public function getOptions();

    /**
     * Get plugin class name.
     *
     * @param $plugin_id
     *   The plugin identifier.
     *
     * @return bool|string
     *   Return the plugin class name; otherwise false.
     */
    public function getClassname($plugin_id);

    /**
     * Create the plugin instance.
     *
     * @param $name
     *   The plugin machine name.
     * @param array $configurations
     *   The plugin configurations.
     *
     * @return \Pr0jectX\Px\PluginInterface
     *   The instantiated plugin class.
     *
     */
    public function createInstance($name, array $configurations);

    /**
     * Discover the plugin classes.
     *
     * @return array
     *   An array of plugin classnames.
     */
    public function discoverPluginClasses();
}
