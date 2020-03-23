<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Robo\Contract\TaskInterface;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\Result;

trait CommonCommandTrait
{
    /**
     * Run the robo task silently.
     *
     * @param \Robo\Contract\TaskInterface $task
     *   The Robo task instance.
     *
     * @return \Robo\Result
     */
    protected function runSilentCommand(TaskInterface $task): Result
    {
        return $task->printOutput(false)
            // This is weird as you would expect this to give you more
            // information, but it suppresses the exit code from display.
            ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
            ->run();
    }
}
