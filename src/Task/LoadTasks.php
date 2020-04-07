<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Task;

use Robo\Collection\CollectionBuilder;

/**
 * Define all the project-x tasks.
 */
trait LoadTasks
{
    /**
     * Define execute command task.
     *
     * @param string $executable
     *   The executable binary.
     *
     * @return \Robo\Collection\CollectionBuilder
     */
    protected function taskExecCommand(string $executable): CollectionBuilder
    {
        return $this->task(ExecCommand::class, $executable);
    }
}
