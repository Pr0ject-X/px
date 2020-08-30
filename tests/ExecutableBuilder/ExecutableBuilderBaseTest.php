<?php

namespace Pr0jectX\Px\Tests\ExecutableBuilder;

use Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase;
use Pr0jectX\Px\Tests\TestCaseBase;

/**
 * Define the executable builder base class.
 */
class ExecutableBuilderBaseTest extends TestCaseBase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    protected $executableBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->executableBuilder = $this
            ->getMockForAbstractClass(ExecutableBuilderBase::class);
    }

    public function testArgument(): void
    {
        $this->executableBuilder->setArgument('testing');
        $this->assertEquals(
            'testing',
            $this->executableBuilder->build()
        );
    }

    public function testArguments(): void
    {
        $this->executableBuilder->setArguments(['one', 'two', 'three']);
        $this->assertEquals(
            'one two three',
            $this->executableBuilder->build()
        );
    }

    public function testSetOption(): void
    {
        $this->executableBuilder->setOption('flag');
        $this->assertEquals(
            '--flag',
            $this->executableBuilder->build()
        );

        $this->executableBuilder->setOption('flag-value', 'value');
        $this->assertEquals(
            '--flag --flag-value="value"',
            $this->executableBuilder->build()
        );
    }

    public function testSetOptions(): void
    {
        $this->executableBuilder->setOptions([
            'flag-value' => 'value',
            'flag-value-two' => 'value-two',
        ]);
        $this->assertEquals(
            '--flag-value="value" --flag-value-two="value-two"',
            $this->executableBuilder->build()
        );
    }

    public function testBuild(): void
    {
        $this->assertEmpty($this->executableBuilder->build());

        $this->executableBuilder
            ->setArgument('arg1')
            ->setOption('item', 'value');

        $this->assertEquals(
            '--item="value" arg1',
            $this->executableBuilder->build()
        );
    }
}
