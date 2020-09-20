<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Mocks\Plugins\Workflow;

use Pr0jectX\Px\Contracts\WorkflowPluginInterface;
use Pr0jectX\Px\Workflow\Workflow;

/**
 * Define the demo workflow.
 */
class DemoWorkflow implements WorkflowPluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'demo-test';
    }

    /**
     * {@inheritDoc}
     */
    public function instance(): Workflow
    {
        return new Workflow([
            'label' => 'Demo Test',
            'jobs' => []
        ]);
    }
}
