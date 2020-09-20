<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Workflow;

use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\Tests\Mocks\Plugins\Workflow\DemoWorkflow;
use Pr0jectX\Px\Tests\TestCaseBase;
use Pr0jectX\Px\Workflow\Workflow;
use Pr0jectX\Px\Workflow\WorkflowManager;
use Robo\ClassDiscovery\RelativeNamespaceDiscovery;
use Robo\Collection\CollectionBuilder;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Define the workflow manager test.
 */
class WorkflowManagerTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\Workflow\WorkflowManager
     */
    protected $workflowManager;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $classDiscovery = $this
            ->getMockBuilder(RelativeNamespaceDiscovery::class)
            ->onlyMethods(['setSearchPattern', 'getClasses'])
            ->disableOriginalConstructor()
            ->getMock();

        $classDiscovery->method('setSearchPattern')
            ->will($this->returnSelf());
        $classDiscovery->method('getClasses')
            ->willReturn([DemoWorkflow::class]);

        $this->workflowManager = new WorkflowManager(
            PxApp::getConfiguration(),
            $classDiscovery
        );
    }

    public function testGetWorkflows(): void
    {
        $workflows = $this
            ->workflowManager
            ->getWorkflows();

        foreach ($workflows as $workflow) {
            $this->assertInstanceOf(
                Workflow::class,
                $workflow
            );
        }
    }

    public function testRunWorkflow(): void
    {
        $collection = $this->getMockBuilder(CollectionBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $task = $this->getMockBuilder(BaseTask::class)
            ->getMock();

        $collection->method('run')
            ->willReturn(new Result($task, 0));

        $this->assertEquals(['success' => [
            'system',
            'installer'
        ]], $this->workflowManager->runWorkflow('setup-local', $collection));
    }

    public function testGetWorkflowOptions(): void
    {
        $options = $this
            ->workflowManager
            ->getWorkflowOptions();

        $validKeys = [
            'setup-local',
            'setup-remote',
            'demo-test',
        ];

        foreach ($validKeys as $key) {
            $this->assertArrayHasKey($key, $options);
        }
    }
}
