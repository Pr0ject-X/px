<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\Workflow;

use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\Tests\TestCaseBase;
use Pr0jectX\Px\Workflow\Workflow;
use Robo\Collection\CollectionBuilder;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Define the workflow test case.
 */
class WorkflowTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\Workflow\Workflow
     */
    protected $workflow;

    /**
     * @var \Robo\Collection\CollectionBuilder
     */
    protected $collection;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = PxApp::getConfiguration();
        $definition = $config->get('workflow.setup-local');

        $this->workflow = new Workflow(
            $definition
        );
    }

    public function testGetLabel(): void
    {
        $this->assertEquals(
            'Setup Local',
            $this->workflow->getLabel()
        );
    }

    public function testRun(): void
    {
        $expectedKeys = ['system', 'installer'];

        $collection = $this
            ->getMockBuilder(CollectionBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $task = $this->getMockBuilder(BaseTask::class)
            ->getMockForAbstractClass();

        $collection->method('run')
            ->willReturn(new Result($task, 0));

        $status = $this->workflow->run($collection);

        $this->assertIsArray($status);

        foreach ($status as $jobName => $result) {
            $this->assertContains(
                $jobName,
                $expectedKeys
            );
            $this->assertEquals(0, $result->getExitCode());
            $this->assertInstanceOf(Result::class, $result);
        }
    }
}
