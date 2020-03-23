<?php

namespace Pr0jectX\Px;

use Robo\ClassDiscovery\ClassDiscoveryInterface;

/**
 * Define the deploy type plugin manager.
 */
class DeployTypePluginManager extends DefaultPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function discover(ClassDiscoveryInterface $classDiscovery): array
    {
        if (empty($this->pluginClasses)) {
            $this->pluginClasses = $classDiscovery
                ->setRelativeNamespace('ProjectX\Plugin\DeployType')
                ->setSearchPattern('/.*DeployType?\.php$/')
                ->getClasses();
        }

        return $this->pluginClasses;
    }
}
