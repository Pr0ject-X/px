<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\CommandTasksBase;

/**
 * Define the environment command.
 */
class Environment extends CommandTasksBase
{
    /**
     * Initialize the project environment.
     */
    public function envInit()
    {
        $this->createInstance()->init();
    }

    /**
     * Display project environment status.
     *
     * @aliases env:info
     */
    public function envStatus()
    {
        $this->createInstance()->info();
    }

    /**
     * Start the project environment.
     *
     * @aliases env:up
     */
    public function envStart()
    {
        $this->createInstance()->start();
    }

    /**
     * @hook post-command env:start
     */
    public function postEnvStart()
    {
        if ($this->confirm('Launch the environment?', true)) {
            /** @var \Symfony\Component\Console\Application $applicaiton */
            $application = PxApp::service('application');

            if ($command = $application->find('env:launch')) {
                $this->taskSymfonyCommand($command)->run();
            }
        }

        $this->createInstance()->setStatus(
            EnvironmentTypeInterface::ENVIRONMENT_STATUS_RUNNING
        );
    }

    /**
     * Stop the project environment.
     *
     * @aliases env:down, env:halt
     */
    public function envStop()
    {
        $this->createInstance()->stop();
    }

    /**
     * @hook post-command env:stop
     */
    public function postEnvStop()
    {
        $this->createInstance()->setStatus(
            EnvironmentTypeInterface::ENVIRONMENT_STATUS_STOPPED
        );
    }

    /**
     * Restart the project environment.
     */
    public function envRestart()
    {
        $this->createInstance()->restart();
    }

    /**
     * Destroy the project environment.
     */
    public function envDestroy()
    {
        $this->createInstance()->destroy();
    }

    /**
     * SSH into the project environment.
     *
     * @aliases ssh
     */
    public function envSsh()
    {
        $this->createInstance()->ssh();
    }

    /**
     * Launch the project environment in a browser.
     *
     * @param array $opts
     * @option $schema
     *   The URL protocol to use.
     */
    public function envLaunch(array $opts = ['schema' => null])
    {
        $this->createInstance()->launch($opts);
    }

    /**
     * Execute command in the project environment.
     *
     * @param string $cmd
     *   The command to execute.
     */
    public function envExecute(string $cmd)
    {
        $this->createInstance()->exec($cmd);
    }

    /**
     * Create the environment instance.
     *
     * @param array $config
     *   An array of env configurations.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\PluginInterface
     *   The environment plugin instance.
     */
    protected function createInstance(array $config = []): PluginInterface
    {
        return Pxapp::getEnvironmentInstance($config);
    }
}
