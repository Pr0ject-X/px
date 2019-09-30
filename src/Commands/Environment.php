<?php

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
use Pr0jectX\Px\PxApp;
use Droath\RoboDDev\Task\loadTasks as DDevTasks;
use Pr0jectX\Px\CommandTasksBase;

/**
 * Define the environment command.
 */
class Environment extends CommandTasksBase
{
    use DDevTasks;

    /**
     * Initialize the project environment.
     */
    public function envInit()
    {
        $this->createInstance()->init();

        return $this;
    }

    /**
     * Display project environment info.
     */
    public function envInfo()
    {
        $this->createInstance()->info();

        return $this;
    }

    /**
     * Start the project environment.
     */
    public function envStart()
    {
        $this->createInstance()->start();

        return $this;
    }

    /**
     * Stop the project environment.
     */
    public function envStop()
    {
        $this->createInstance()->stop();

        return $this;
    }

    /**
     * Restart the project environment.
     */
    public function envRestart()
    {
        $this->createInstance()->restart();

        return $this;
    }

    /**
     * Destroy the project environment.
     */
    public function envDestroy()
    {
        $this->createInstance()->destroy();

        return $this;
    }

    /**
     * SSH into the project environment.
     */
    public function envSsh()
    {
        $this->createInstance()->ssh();

        return $this;
    }

    /**
     * Execute command in the project environment.
     *
     * @param $cmd
     *   The command to execute.
     *
     * @return Environment
     * @throws \Exception
     */
    public function envExecute($cmd)
    {
        $this->createInstance()->exec($cmd);

        return $this;
    }

    /**
     * Create the environment instance.
     *
     * @param array $envConfig
     *   An array of env configurations.
     *
     * @return EnvironmentTypeInterface
     *
     * @throws \Exception
     */
    protected function createInstance($envConfig = [])
    {
        $config = PxApp::getConfiguration();

        $envType = $config->has('environment.type')
            ? $config->get('environment.type')
            : null;

        if (!isset($envType)) {
            throw new \Exception(
                sprintf('The %s environment type is not found.')
            );
        }

        return $this->envTypePluginManager()
            ->createInstance($envType, $envConfig);
    }

    /**
     * Environment type plugin manager.
     *
     * @return \Pr0jectX\Px\EnvironmentTypePluginManager
     */
    protected function envTypePluginManager()
    {
        return PxApp::getContainer()->get('environmentTypePluginManager');
    }
}
