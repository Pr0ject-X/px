<?php

namespace Pr0jectX\Px\Exception;

use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;

/**
 * Define the environment method not support exception.
 */
class EnvironmentMethodNotSupported extends \RuntimeException
{
    /**
     * The exception constructor.
     *
     * @param EnvironmentTypeInterface $environmentType
     *   The environment type object.
     * @param $method
     *   The environment method that's being called.
     */
    public function __construct(EnvironmentTypeInterface $environmentType, $method)
    {
        $class = get_class($environmentType);
        $pluginLabel = $class::pluginLabel();

        parent::__construct(
            sprintf('The %s environment type does not support %s.', $pluginLabel, $method)
        );
    }
}
