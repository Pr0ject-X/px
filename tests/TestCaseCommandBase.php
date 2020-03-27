<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\PxApp;
use Robo\Runner;

/**
 * Define the test case command base class.
 */
abstract class TestCaseCommandBase extends TestCaseBase
{
    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        (new Runner())->registerCommandClasses(
            $this->app,
            $this->commandClasses()
        );
    }

    /**
     * @return array
     */
    abstract protected function commandClasses(): array;

    /**
     * Set the command inputs.
     *
     * @param array $inputs
     *   An array of console inputs.
     *
     * @return \Pr0jectX\Px\Tests\TestCaseCommandBase
     */
    protected function setCommandInputs(array $inputs): TestCaseCommandBase
    {
        /** @var \Symfony\Component\Console\Input\Input $input */
        $input = PxApp::service('input');
        $stream = fopen('php://memory', 'r+', false);

        foreach ($inputs as $value) {
            fwrite($stream, $value . PHP_EOL);
        }
        rewind($stream);

        $input->setStream($stream);

        return $this;
    }
}
