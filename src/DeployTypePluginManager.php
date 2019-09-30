<?php

namespace Pr0jectX\Px;

/**
 * Define the deploy type plugin manager.
 */
class DeployTypePluginManager extends DefaultPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function discoverPluginClasses()
    {
        /** @var \Robo\ClassDiscovery\RelativeNamespaceDiscovery $classDiscovery */
        $classDiscovery = PxApp::service('relativeNamespaceDiscovery');

        $classDiscovery
            ->setRelativeNamespace('ProjectX\Plugin\DeployType')
            ->setSearchPattern('/.*DeployType?\.php$/');

        return $classDiscovery->getClasses();
    }
}
