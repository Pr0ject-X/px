<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\Commands\Environment;
use Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder;
use Pr0jectX\Px\Exception\EnvironmentMethodNotSupported;
use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;
use Pr0jectX\Px\PxApp;

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
    public function start(array $opts = [])
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
    public function exec(string $cmd)
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
    public function envPackages(): array
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
    public function pluginConfiguration(): ConfigTreeBuilder
    {
        $envType = PxApp::getEnvironmentType();
        $envOptions = $this->pluginManager->getOptions();
        return (new ConfigTreeBuilder())
            ->setQuestionInput($this->input)
            ->setQuestionOutput($this->output)
            ->createNode('type')
                ->setValue($this->choice(
                    'Select the environment type',
                    $envOptions,
                    $envType
                ))
            ->end();
    }
}
