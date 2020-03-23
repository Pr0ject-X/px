<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

/**
 * Define the command type plugin manager.
 */
class CommandTypePluginManager extends DefaultPluginManager
{
    /**
     * @inheritDoc
     */
    public function discover(): array
    {
        return $this->classDiscovery
            ->setRelativeNamespace('ProjectX\Plugin\CommandType')
            ->setSearchPattern('/.*CommandType?\.php$/')
            ->getClasses();
    }
}
