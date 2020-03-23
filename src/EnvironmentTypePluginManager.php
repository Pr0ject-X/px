<?php

namespace Pr0jectX\Px;

use Robo\ClassDiscovery\ClassDiscoveryInterface;

/**
 * Define the environment type plugin manager.
 */
class EnvironmentTypePluginManager extends DefaultPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function discover(ClassDiscoveryInterface $classDiscovery): array
    {
        if (empty($this->pluginClasses)) {
            $this->pluginClasses = $classDiscovery
                ->setRelativeNamespace('ProjectX\Plugin\EnvironmentType')
                ->setSearchPattern('/.*EnvironmentType?\.php$/')
                ->getClasses();
        }

        return $this->pluginClasses;
    }
}
