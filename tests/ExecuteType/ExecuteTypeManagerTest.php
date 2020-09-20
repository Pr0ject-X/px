<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\ExecuteType;

use Pr0jectX\Px\Commands\Environment;
use Pr0jectX\Px\ExecuteType\ExecuteShellType;
use Pr0jectX\Px\ExecuteType\ExecuteSymfonyType;
use Pr0jectX\Px\ExecuteType\ExecuteTypeManager;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\Tests\TestCaseBase;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Robo\Result;
use Robo\Runner;
use Robo\Task\BaseTask;

/**
 * Define the execute type manager test.
 */
class ExecuteTypeManagerTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\ExecuteType\ExecuteTypeManager
     */
    protected $executeTypeManager;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        (new Runner())
            ->registerCommandClasses(
                PxApp::service('application'),
                [Environment::class]
            );

        $this->executeTypeManager = new ExecuteTypeManager();
    }

    /**
     * Execute types.
     *
     * @return array|string[]
     */
    public function executeTypes(): array
    {
        return [
            ['shell', ExecuteShellType::class],
            ['symfony', ExecuteSymfonyType::class]
        ];
    }

    /**
     * @dataProvider executeTypes
     *
     * @param string $executeType
     * @param string $expected
     */
    public function testCreateInstance(
        string $executeType,
        string $expected
    ): void
    {
        $definition = [
            'command' => 'echo "Testing";'
        ];

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf(
            $expected,
            $this->executeTypeManager->createInstance(
                $executeType,
                $definition
            )
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The "bogus" execute type is invalid!'
        );

        $this->executeTypeManager->createInstance(
            'bogus',
            []
        );
    }

    public function testExecuteInstances(): void
    {
        $collection = $this->getMockBuilder(CollectionBuilder::class)
            ->onlyMethods(['addTask', 'run'])
            ->disableOriginalConstructor()
            ->getMock();

        $task = $this->getMockBuilder(BaseTask::class)
            ->getMockForAbstractClass();

        $collection->expects($this->once())
            ->method('run')
            ->willReturn(new Result($task, 0));

        $collection
            ->expects($this->exactly(2))
            ->method('addTask')
            ->willReturnCallback(function($value) {
                $this->assertInstanceOf(TaskInterface::class, $value);
            });

        $executeTypes = [
            new ExecuteShellType(['command' => 'echo "Shell Test";']),
            new ExecuteSymfonyType(['command' => 'env:status'])
        ];

        $result = $this->executeTypeManager
            ->executeInstances($executeTypes, $collection);

        $this->assertEquals(0, $result->getExitCode());
    }
}
