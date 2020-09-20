<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecuteType;

use Robo\Task\Base\Exec;
use Robo\Task\BaseTask;

/**
 * Define the shell execute type.
 */
class ExecuteShellType extends ExecuteTypeBase
{
    /**
     * {@inheritDoc}
     */
    public function createTaskInstance(): BaseTask
    {
        return (new Exec($this->command))
            ->args($this->getArguments())
            ->options($this->getOptions());
    }
}
