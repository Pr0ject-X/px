<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Workflow;

use Pr0jectX\Px\Contracts\ExecuteTypeInterface;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\Tests\TestCaseBase;
use Pr0jectX\Px\Workflow\WorkflowJob;

class WorkflowJobTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\Workflow\WorkflowJob
     */
    protected $workflowJob;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $config = PxApp::getConfiguration();
        $definition = $config->get('workflow.setup-local');

        $this->workflowJob = new WorkflowJob(
            'system',
            $definition['jobs']['system']
        );
    }

    public function testGetName(): void
    {
        $this->assertEquals(
            'system',
            $this->workflowJob->getName()
        );
    }

    public function testGetLabel(): void
    {
        $this->assertEquals(
            'System',
            $this->workflowJob->getLabel()
        );
    }

    public function testGetCommandExecuteTypes(): void
    {
        foreach ($this->workflowJob->getCommandExecuteTypes() as $executeType) {
            $this->assertInstanceOf(ExecuteTypeInterface::class, $executeType);
        }
    }
}
