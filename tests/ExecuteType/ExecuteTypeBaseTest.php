<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests\HookExecute;

use Pr0jectX\Px\ExecuteType\ExecuteTypeBase;
use Pr0jectX\Px\Tests\TestCaseBase;

/**
 * Define the execute type base test.
 */
class ExecuteTypeBaseTest extends TestCaseBase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ExecuteTypeBase
     */
    protected $executeTypeBase;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->executeTypeBase = $this->getMockBuilder(ExecuteTypeBase::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    public function testIsValid(): void
    {
        $this->assertFalse(
            $this->executeTypeBase->isValid()
        );

        $this->executeTypeBase->setCommand('echo "test";');

        $this->assertTrue(
            $this->executeTypeBase->isValid()
        );
    }

    /**
     * @covers ExecuteTypeBase::setCommand()
     * @covers ExecuteTypeBase::getCommand()
     */
    public function testSetCommand(): void
    {
        $this->executeTypeBase->setCommand(
            'echo "test set command";'
        );

        $this->assertEquals(
            'echo "test set command";',
            $this->executeTypeBase->getCommand()
        );
    }

    /**
     * @covers ExecuteTypeBase::setOptions()
     * @covers ExecuteTypeBase::getOptions()
     */
    public function testSetOptions(): void
    {
        $this->executeTypeBase->setOptions(
            ['test', 'item' => 'value']
        );

        $this->assertEquals(
           ['test' => null, 'item' => 'value'],
           $this->executeTypeBase->getOptions()
        );
    }

    public function testSetArguments(): void
    {
        $this->executeTypeBase->setArguments(
            ['arg', 'arg1', 'arg2']
        );

        $this->assertEquals(
            ['arg', 'arg1', 'arg2'],
            $this->executeTypeBase->getArguments()
        );
    }
}
