<?php

namespace Pr0jectX\Px;

/**
 * Define the environment type plugin manager.
 */
class EnvironmentTypePluginManager extends DefaultPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function discoverPluginClasses()
    {
        /** @var \Robo\ClassDiscovery\RelativeNamespaceDiscovery $classDiscovery */
        $classDiscovery = PxApp::service('relativeNamespaceDiscovery');

        $classDiscovery
            ->setRelativeNamespace('ProjectX\Plugin\EnvironmentType')
            ->setSearchPattern('/.*EnvironmentType?\.php$/');

        return $classDiscovery->getClasses();
    }
}
