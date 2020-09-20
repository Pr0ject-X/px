<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\HookExecute;

use Consolidation\AnnotatedCommand\AnnotationData;
use Consolidation\AnnotatedCommand\CommandData;
use Pr0jectX\Px\Commands\Environment;
use Pr0jectX\Px\ExecuteType\ExecuteTypeManager;
use Pr0jectX\Px\HookExecute\HookExecuteManager;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\Tests\TestCaseBase;
use Robo\Collection\CollectionBuilder;
use Robo\Result;
use Robo\Runner;
use Robo\Task\BaseTask;
use Robo\Tasks;

/**
 * Define the hook execute manager test.
 */
class HookExecuteManagerTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\HookExecute\HookExecuteManager
     */
    protected $hookExecuteManager;

    public function setUp(): void
    {
        parent::setUp();

        (new Runner())
            ->registerCommandClasses(
                PxApp::service('application'),
                [Environment::class]
            );

        $this->setProjectXConfiguration();

        $config = PxApp::getConfiguration();

        $commandData = $this
            ->getMockBuilder(CommandData::class)
            ->onlyMethods(['annotationData'])
            ->disableOriginalConstructor()
            ->getMock();

        $annotationData = $this
            ->getMockBuilder(AnnotationData::class)
            ->onlyMethods(['get'])
            ->getMock();

        $annotationData
            ->method('get')
            ->willReturn('core:bogus');

        $commandData
            ->method('annotationData')
            ->willReturn($annotationData);

        $executeTypeManger = new ExecuteTypeManager();

        $this->hookExecuteManager = new HookExecuteManager(
            $config,
            $commandData,
            $executeTypeManger
        );
    }

    /**
     * Hook providers.
     *
     * @return \string[][]
     */
    public function hookProviders(): array
    {
        return [
            ['pre'],
            ['post']
        ];
    }

    /**
     * @dataProvider hookProviders
     * @param string $hookType
     */
    public function testExecuteCommands(
        string $hookType
    ): void {
        $collection = $this
            ->getMockBuilder(CollectionBuilder::class)
            ->setConstructorArgs([new Tasks()])
            ->getMock();

        $collection
            ->expects($this->exactly(3))
            ->method('addTask')
            ->willReturnCallback(function ($value) {
                $this->assertInstanceOf(BaseTask::class, $value);
            });

        $task = $this->getMockBuilder(BaseTask::class)
            ->getMockForAbstractClass();

        $collection->expects($this->once())
            ->method('run')
            ->willReturn(new Result($task, 0));

        $result = $this->hookExecuteManager
            ->executeCommands($hookType, $collection);

        $this->assertEquals(0, $result->getExitCode());
    }
}
