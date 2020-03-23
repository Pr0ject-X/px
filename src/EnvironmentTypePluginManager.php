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
    public function discover(): array
    {
        return $this->classDiscovery
            ->setRelativeNamespace('ProjectX\Plugin\EnvironmentType')
            ->setSearchPattern('/.*EnvironmentType?\.php$/')
            ->getClasses();
    }
}
