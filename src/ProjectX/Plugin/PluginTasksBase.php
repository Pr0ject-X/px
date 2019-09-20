<?php

namespace Droath\ProjectX\ProjectX\Plugin;

use Robo\Tasks;

/**
 * Define the plugin tasks base.
 */
abstract class PluginTasksBase extends Tasks implements PluginInterface
{
    /**
     * Plugin configurations.
     *
     * @var array
     */
    protected $configurations = [];

    /**
     * The plugin tasks base constructor.
     *
     * @param array $configurations
     *   An array of the plugin configurations.
     */
    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }
}
