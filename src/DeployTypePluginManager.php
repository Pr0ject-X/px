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
    public function discover(): array
    {
        return $this->classDiscovery
            ->setRelativeNamespace('ProjectX\Plugin\DeployType')
            ->setSearchPattern('/.*DeployType?\.php$/')
            ->getClasses();
    }
}
