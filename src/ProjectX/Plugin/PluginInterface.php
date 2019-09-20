<?php

namespace Droath\ProjectX\ProjectX\Plugin;

/**
 * Define plugin type interface.
 */
interface PluginInterface
{
    /**
     * Define the plugin identifier.
     *
     * @return string
     *   The plugin identifier.
     */
    public static function pluginId();

    /**
     * Define the plugin label.
     *
     * @return string
     *   The plugin human readable label.
     */
    public static function pluginLabel();

    /**
     * Get plugin configurations.
     *
     * @return array
     *   An array of the plugin configurations.
     */
    public function getConfigurations();
}
