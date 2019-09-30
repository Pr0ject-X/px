<?php

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\Exception\EnvironmentMethodNotSupported;
use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;

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
    public function exec($cmd)
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
}
