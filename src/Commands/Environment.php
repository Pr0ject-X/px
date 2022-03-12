<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder;
use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
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
    public function envInit(): void
    {
        $this->createInstance()->init();
    }

    /**
     * Install the environment prerequisites.
     */
    public function envInstall(): void
    {
        $this->createInstance()->install();
    }

    /**
     * Display project environment status.
     *
     * @aliases env:info
     */
    public function envStatus(): void
    {
        $this->createInstance()->info();
    }

    /**
     * Start the project environment.
     *
     * @aliases env:up
     */
    public function envStart(): void
    {
        $this->createInstance()->start();
    }

    /**
     * @hook post-command env:start
     */
    public function postEnvStart(): void
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
    public function envStop(): void
    {
        $this->createInstance()->stop();
    }

    /**
     * @hook post-command env:stop
     */
    public function postEnvStop(): void
    {
        $this->createInstance()->setStatus(
            EnvironmentTypeInterface::ENVIRONMENT_STATUS_STOPPED
        );
    }

    /**
     * Restart the project environment.
     */
    public function envRestart(): void
    {
        $this->createInstance()->restart();
    }

    /**
     * Destroy the project environment.
     */
    public function envDestroy(): void
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
    public function envLaunch(array $opts = ['schema' => null]): void
    {
        $this->createInstance()->launch($opts);
    }

    /**
     * Execute command in the project environment.
     *
     * @param string $cmd
     *   The command to execute.
     * @param array $opts
     *   An array of command options.
     *
     * @option $silent
     *   Set if command output should be silent.
     */
    public function envExecute(string $cmd, $opts = [
        'silent' => false,
    ]): void
    {
        $this->createInstance()->exec($cmd, $opts);
    }

    /**
     * Set the project environment type to use.
     */
    public function envSet(): void
    {
        try {
            /** @var \Pr0jectX\Px\PluginManagerInterface $envManager */
            $envManager = PxApp::service('environmentTypePluginManager');

            $config = (new ConfigTreeBuilder())
                ->setQuestionInput($this->input)
                ->setQuestionOutput($this->output)
                ->createNode('environment')
                ->setValue($this->choice(
                    'Select the environment type',
                    $envManager->getOptions(),
                    PxApp::getEnvironmentType()
                ))
                ->end();

            $data = $config->build();

            if ($this->writeConfiguration($data)) {
                $this->success(sprintf(
                    'Environment type "%s" has been set!',
                    $data['environment']
                ));
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Create the environment instance.
     *
     * @param array $config
     *   An array of env configurations.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface
     *   The environment plugin instance.
     */
    protected function createInstance(array $config = []): EnvironmentTypeInterface
    {
        return Pxapp::getEnvironmentInstance($config);
    }
}
