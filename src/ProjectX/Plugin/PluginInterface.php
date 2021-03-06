<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
    public static function pluginId(): string;

    /**
     * Define the plugin label.
     *
     * @return string
     *   The plugin human readable label.
     */
    public static function pluginLabel(): string;

    /**
     * Get plugin configurations.
     *
     * @return array
     *   An array of the plugin configurations.
     */
    public function getConfigurations(): array;

    /**
     * Get the plugin cache instance.
     *
     * @return \Symfony\Component\Cache\Adapter\FilesystemAdapter
     */
    public function getPluginCache(): FilesystemAdapter;
}
