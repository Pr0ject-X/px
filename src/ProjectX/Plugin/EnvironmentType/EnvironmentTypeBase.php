<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\Commands\Environment;
use Pr0jectX\Px\Datastore\JsonDatastore;
use Pr0jectX\Px\Exception\EnvironmentMethodNotSupported;
use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\State\DatastoreState;

/**
 * Define the environment type base class.
 */
abstract class EnvironmentTypeBase extends PluginTasksBase implements EnvironmentTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function init(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function install(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function start(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function setup(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function stop(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function restart(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function info(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function exec(string $cmd, array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function launch(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function ssh(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function debug(array $opts = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function envDatabases(bool $internal = false): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function envPackages(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function execBuilderOptions(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function registeredCommands(): array
    {
        return [
            Environment::class
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus(): string
    {
        return (string) ($this->getState()->get('status') ?? 'unknown');
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus(string $status): EnvironmentTypeBase
    {
        $this->getState()->set('status', $status)->save();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isStopped(): bool
    {
        return $this->getStatus() === EnvironmentTypeInterface::ENVIRONMENT_STATUS_STOPPED;
    }

    /**
     * {@inheritDoc}
     */
    public function isRunning(): bool
    {
        return $this->getStatus() === EnvironmentTypeInterface::ENVIRONMENT_STATUS_RUNNING;
    }

    /**
     * {@inheritDoc}
     */
    public function getState(): DatastoreState
    {
        return new DatastoreState(
            new JsonDatastore(
                PxApp::projectTempDir() . '/state/environment.json'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function selectEnvDatabase(string $name, bool $internal = false): EnvironmentDatabase
    {
        $databases = $this->envDatabases($internal);

        if (
            !isset($databases[$name])
            || !$databases[$name] instanceof EnvironmentDatabase
        ) {
            throw new \InvalidArgumentException(
                sprintf('Unable to locate the %s database!', $name)
            );
        }

        return $databases[$name];
    }
}
