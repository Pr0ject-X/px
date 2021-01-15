<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
use Pr0jectX\Px\PxApp;

/**
 * Define the plugin configuration trait.
 */
trait PluginConfigurationTrait
{
    /**
     * Get plugin configurations.
     *
     * @return array
     *   An array of the plugin configurations.
     */
    public function getPluginConfiguration(): array
    {
        $class = get_class($this);

        $pluginId = $this instanceof EnvironmentTypeInterface
            ? 'environment'
            : $class::pluginId();
        $pluginConfiguration = PxApp::getConfiguration()->get('plugins');

        if (!isset($pluginConfiguration[$pluginId])) {
            return [];
        }

        return $pluginConfiguration[$pluginId];
    }
}
