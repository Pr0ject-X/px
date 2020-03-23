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
     *
     * @param array $opts
     *   An array of start options.
     */
    public function init(array $opts = []);

    /**
     * Start the environment.
     *
     * @param array $opts
     *   An array of start options.
     */
    public function start(array $opts = []);

    /**
     * Stop the environment.
     *
     * @param array $opts
     *   An array of stop options.
     */
    public function stop(array $opts = []);

    /**
     * Restart the environment.
     *
     * @param array $opts
     *   An array of restart options.
     */
    public function restart(array $opts = []);

    /**
     * Destroy the environment.
     *
     * @param array $opts
     *   An array of destroy options.
     */
    public function destroy(array $opts = []);

    /**
     * Display environment information.
     *
     * @param array $opts
     *   An array of info options.
     */
    public function info(array $opts = []);

    /**
     * Connection to the environment using SSH.
     *
     * @param array $opts
     *   An array of ssh options.
     */
    public function ssh(array $opts = []);

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
     * @param array $opts
     *   An array of launch options.
     */
    public function launch(array $opts = []);

    /**
     * Define the environment application root.
     *
     * @return string
     *   The path to the environment application root.
     */
    public function envAppRoot(): string;

    /**
     * Define the environment packages.
     *
     * @return array
     *   An array of environment packages.
     */
    public function envPackages(): array;
}
