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
    public function init()
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function start()
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function stop()
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function restart()
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy()
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function info()
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
    public function launch(array $options = [])
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function ssh()
    {
        throw new EnvironmentMethodNotSupported($this, __FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function envPackages() : array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function registeredCommands() : array
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
        $envOptions = $this->pluginManager->getOptions(['localhost']);

        return (new ConfigTreeBuilder())
            ->setQuestionInput($this->input)
            ->setQuestionOutput($this->output)
            ->createNode('type')
                ->setValue($this->choice(
                    'Select the environment type', $envOptions, $envType !== 'localhost' ? $envType : ''
                ))
            ->end();
    }

}
