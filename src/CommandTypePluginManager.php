<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Robo\ClassDiscovery\ClassDiscoveryInterface;

/**
 * Define the command type plugin manager.
 */
class CommandTypePluginManager extends DefaultPluginManager
{
    /**
     * @inheritDoc
     */
    public function discover(ClassDiscoveryInterface $classDiscovery): array
    {
        if (empty($this->pluginClasses)) {
            $this->pluginClasses = $classDiscovery
                ->setRelativeNamespace('ProjectX\Plugin\CommandType')
                ->setSearchPattern('/.*CommandType?\.php$/')
                ->getClasses();
        }

        return $this->pluginClasses;
    }
}
