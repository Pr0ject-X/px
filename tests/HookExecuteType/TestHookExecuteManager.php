<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\HookExecuteType;

use Consolidation\AnnotatedCommand\AnnotatedCommand;
use Consolidation\AnnotatedCommand\AnnotationData;
use Consolidation\AnnotatedCommand\CommandData;
use Pr0jectX\Px\Commands\Environment;
use Pr0jectX\Px\HookExecuteType\HookExecuteManager;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\Tests\TestCaseBase;
use Robo\Runner;

class TestHookExecuteManager extends TestCaseBase
{
    protected $hookExecuteManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->setProjectXConfiguration();

        (new Runner())->registerCommandClasses(
            $this->app,
            [Environment::class]
        );
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

        $annotationData->expects($this->exactly(2))
            ->method('get')
            ->willReturn('core:bogus');

        $commandData->expects($this->exactly(2))
            ->method('annotationData')
            ->willReturn($annotationData);

        $this->hookExecuteManager = new HookExecuteManager(
            $config,
            $commandData
        );
    }

    public function testBuildCommands()
    {
        $preCommands = $this
            ->hookExecuteManager
            ->buildCommands('pre');

        $this->assertEquals(
            'echo "Hello Pre World 1"',
            $preCommands[0]['command']
        );
        $this->assertInstanceOf(
            AnnotatedCommand::class,
            $preCommands[1]['command']
        );
        $this->assertEquals(
            'echo "Hello Pre World 2"',
            $preCommands[2]['command']
        );
        $this->assertCount(3, $preCommands);

        $postCommands = $this
            ->hookExecuteManager
            ->buildCommands('post');

        $this->assertEquals(
            'echo "Hello Post World 1"',
            $postCommands[0]['command']
        );
        $this->assertInstanceOf(
            AnnotatedCommand::class,
            $postCommands[1]['command']
        );
        $this->assertEquals(
            'echo "Hello Post World 2"',
            $postCommands[2]['command']
        );
        $this->assertCount(3, $postCommands);
    }
}
