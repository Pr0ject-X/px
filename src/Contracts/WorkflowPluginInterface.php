<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Contracts;

use Pr0jectX\Px\Workflow\Workflow;

/**
 * Define the workflow definition interface.
 */
interface WorkflowPluginInterface
{
    /**
     * Define the workflow plugin name.
     *
     * @return string
     *   A unique workflow name.
     */
    public function name(): string;

    /**
     * Define the workflow plugin instance.
     *
     * @return \Pr0jectX\Px\Workflow\Workflow
     *   The workflow instance.
     */
    public function instance(): Workflow;
}
