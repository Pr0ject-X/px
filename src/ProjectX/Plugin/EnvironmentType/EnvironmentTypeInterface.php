<?php

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;

/**
 * Define the environment type interface.
 */
interface EnvironmentTypeInterface extends PluginInterface {

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
     * @param $cmd
     *   The command to execute.
     */
    public function exec($cmd);
}
