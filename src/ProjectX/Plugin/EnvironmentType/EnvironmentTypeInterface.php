<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\ProjectX\Plugin\PluginCommandRegisterInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginConfigurationBuilderInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;

/**
 * Define the environment type interface.
 */
interface EnvironmentTypeInterface extends PluginInterface, PluginConfigurationBuilderInterface, PluginCommandRegisterInterface
{
    /**
     * Initial the environment.
     */
    public function init();

    /**
     * Start the environment.
     */
    public function start();

    /**
     * Stop the environment.
     */
    public function stop();

    /**
     * Restart the environment.
     */
    public function restart();

    /**
     * Destroy the environment.
     */
    public function destroy();

    /**
     * Display environment information.
     */
    public function info();

    /**
     * Connection to the environment using SSH.
     */
    public function ssh();

    /**
     * Execute a command on the environment.
     *
     * @param string $cmd
     *   The command to execute.
     */
    public function exec(string $cmd);

    /**
     * Launch the environment application.
     *
     * @param array $options
     *   An array of launch options.
     */
    public function launch(array $options = []);

    /**
     * Define the environment application root.
     *
     * @return string
     *   The path to the environment application root.
     */
    public function envAppRoot() : string;

    /**
     * Define the environment packages.
     *
     * @return array
     *   An array of environment packages.
     */
    public function envPackages(): array;
}
