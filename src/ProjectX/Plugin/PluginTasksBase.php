<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Pr0jectX\Px\IOExtraTrait;
use Pr0jectX\Px\PluginManagerInterface;
use Robo\Tasks;

/**
 * Define the plugin tasks base.
 */
abstract class PluginTasksBase extends Tasks implements PluginInterface
{
    use IOExtraTrait;

    /**
     * @var \Pr0jectX\Px\PluginManagerInterface
     */
    protected $pluginManager;

    /**
     * Plugin configurations.
     *
     * @var array
     */
    protected $configurations = [];

    /**
     * The plugin tasks base constructor.
     *
     * @param \Pr0jectX\Px\PluginManagerInterface $plugin_manager
     *   The plugin manager instance.
     * @param array $configurations
     *   An array of the plugin configurations.
     */
    public function __construct(
        PluginManagerInterface $plugin_manager,
        array $configurations
    )
    {
        $this->pluginManager = $plugin_manager;
        $this->configurations = $configurations;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurations() : array
    {
        return $this->configurations;
    }
}
