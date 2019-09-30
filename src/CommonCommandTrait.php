<?php

namespace Pr0jectX\Px;

use Robo\Contract\TaskInterface;
use Robo\Contract\VerbosityThresholdInterface;

trait CommonCommandTrait
{
    /**
     * Run the robo task silently.
     */
    protected function runSilentCommand(TaskInterface $task)
    {
        return $task->printOutput(false)
            // This is weird as you would expect this to give you more
            // information, but it suppresses the exit code from display.
            ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
            ->run();
    }
}
