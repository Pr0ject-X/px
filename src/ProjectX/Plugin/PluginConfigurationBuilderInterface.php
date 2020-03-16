<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder;

/**
 * Define the plugin configuration builder interface.
 */
interface PluginConfigurationBuilderInterface
{
    /**
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder
     */
    public function pluginConfiguration() : ConfigTreeBuilder;
}
