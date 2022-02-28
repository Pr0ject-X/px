<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

use Pr0jectX\Px\PxApp;

/**
 * Define the localhost environment type.
 */
class LocalhostEnvironmentType extends EnvironmentTypeBase
{
    /**
     * @inheritDoc
     */
    public static function pluginId(): string
    {
        return 'localhost';
    }

    /**
     * @inheritDoc
     */
    public static function pluginLabel(): string
    {
        return 'Localhost';
    }

    /**
     * @inheritDoc
     */
    public function envAppRoot(): string
    {
        return PxApp::projectRootPath();
    }

    /**
     * @inheritDoc
     */
    public function envDatabases(): array
    {
        return [];
    }
}
