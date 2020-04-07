<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Task;

use Robo\Common\ExecOneCommand;
use Robo\Exception\TaskException;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Process\Process;

/**
 * Define the executable command.
 */
class ExecCommand extends BaseTask
{
    use ExecOneCommand;

    /**
     * @var string
     */
    protected $subCommand;

    /**
     * @var string
     */
    protected $executable;

    public function __construct($executable)
    {
        $this->executable = $this->findExecutable($executable);

        if (!$this->executable) {
            throw new TaskException(
                __CLASS__,
                sprintf('Unable to locate the %s executable.', $this->executable)
            );
        }
    }

    /**
     * Set the sub-command.
     *
     * @param string $subCommand
     *   The sub-command string.
     *
     * @return \Pr0jectX\Px\Task\ExecCommand
     */
    public function setSubCommand(string $subCommand): ExecCommand
    {
        $this->subCommand = $subCommand;

        return $this;
    }

    /**
     * Retrieve the full executable command.
     *
     * @return string
     *   The executable command.
     */
    public function getCommand(): string
    {
        return trim(implode(' ', array_map('trim', $this->commandStructure())));
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $resultData = $this->execute(
            new Process($this->getCommand(), getcwd())
        );

        return new Result(
            $this,
            $resultData->getExitCode(),
            $resultData->getMessage(),
            $resultData->getData()
        );
    }

    /**
     * Structure the command output.
     *
     * @return array
     *   An array of the parts that make up the command.
     */
    protected function commandStructure(): array
    {
        return [
            $this->executable,
            $this->subCommand,
            $this->arguments,
        ];
    }
}
