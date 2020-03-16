<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Pr0jectX\Px\CommandTasksBase;

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
}
