<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Pr0jectX\Px\CommandTasksBase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Define the plugin command task base.
 */
abstract class PluginCommandTaskBase extends CommandTasksBase
{
    protected $plugin;

    public function __construct(PluginInterface $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Retrieve the plugin cache instance.
     *
     * @return \Symfony\Component\Cache\Adapter\FilesystemAdapter
     */
    protected function pluginCache(): FilesystemAdapter
    {
        return $this->plugin->getPluginCache();
    }

    /**
     * Get the plugin instance.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\PluginInterface
     */
    protected function getPlugin(): PluginInterface
    {
        return $this->plugin;
    }
}
