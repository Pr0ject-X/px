<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\Contracts\ExecutableBuilderConfigurableInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginCommandRegisterInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Pr0jectX\Px\State\DatastoreState;

/**
 * Define the environment type interface.
 */
interface EnvironmentTypeInterface extends
    PluginInterface,
    PluginCommandRegisterInterface,
    ExecutableBuilderConfigurableInterface
{
    /**
     * @var string
     */
    public const ENVIRONMENT_DB_PRIMARY = 'primary';

    /**
     * @var string
     */
    public const ENVIRONMENT_DB_SECONDARY = 'secondary';

    /**
     * @var string
     */
    public const ENVIRONMENT_STATUS_RUNNING = 'running';

    /**
     * @var string
     */
    public const ENVIRONMENT_STATUS_STOPPED = 'stopped';

    /**
     * Initial the environment.
     *
     * @param array $opts
     *   An array of start options.
     */
    public function init(array $opts = []);

    /**
     * Install the environment prerequisites.
     *
     * @param array $opts
     *   An array of start options.
     */
    public function install(array $opts = []);

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
     * @param array $opts
     *   An array of execute options.
     */
    public function exec(string $cmd, array $opts = []);

    /**
     * Launch the environment application.
     *
     * @param array $opts
     *   An array of launch options.
     */
    public function launch(array $opts = []);

    /**
     * Get the environment status.
     */
    public function getStatus(): string;

    /**
     * Set the environment status.
     *
     * @param string $status
     *   The environment status.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeBase
     */
    public function setStatus(string $status);

    /**
     * Get the environment state instance.
     *
     * @return \Pr0jectX\Px\State\DatastoreState
     *   The datastore environment state instance.
     */
    public function getState(): DatastoreState;

    /**
     * Determine if the environment is running.
     *
     * @return bool
     *   Return true if environment is running; otherwise false.
     */
    public function isRunning(): bool;

    /**
     * Determine if the environment is stopped.
     *
     * @return bool
     *   Return true if environment is stopped; otherwise false.
     */
    public function isStopped(): bool;

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

    /**
     * Define the environment databases.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase[]
     *   An array of environment databases.
     */
    public function envDatabases(): array;

    /**
     * Select the environment database.
     *
     * @param string $name
     *   The name of the database key.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function selectEnvDatabase(string $name): EnvironmentDatabase;
}
