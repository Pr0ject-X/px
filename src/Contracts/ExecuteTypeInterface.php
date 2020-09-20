<?php

namespace Pr0jectX\Px\Contracts;

use Robo\Task\BaseTask;

/**
 * Define the execute type interface.
 */
interface ExecuteTypeInterface
{
    /**
     * Validate if execute type is valid.
     *
     * @return bool
     *   Return true if execute type is valid; otherwise false.
     */
    public function isValid(): bool;

    /**
     * Set the execute type command.
     *
     * @param string $command
     *   The execute type command.
     *
     * @return self
     */
    public function setCommand(string $command): self;

    /**
     * Get the execute type command.
     *
     * @return string
     */
    public function getCommand(): string;

    /**
     * Set the execute type options.
     *
     * @param array $options
     *   An array of command options.
     *
     * @return \Pr0jectX\Px\ExecuteType\ExecuteTypeBase
     */
    public function setOptions(array $options): self;

    /**
     * Get the execute type options.
     *
     * @return array
     *   An array of options.
     */
    public function getOptions(): array;

    /**
     * Set the execute type arguments.
     *
     * @param array $arguments
     *   An array of command arguments.
     *
     * @return self
     */
    public function setArguments(array $arguments): self;

    /**
     * Get the execute type arguments.
     *
     * @return array
     *   An array of arguments.
     */
    public function getArguments(): array;

    /**
     * Create the execute type task instance.
     *
     * @return \Robo\Task\BaseTask
     *   The base task instance.
     */
    public function createTaskInstance(): BaseTask;
}
