<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecuteType;

use Pr0jectX\Px\PxApp;
use Robo\Task\Base\SymfonyCommand;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Command\Command;

/**
 * Define the execute symfony type.
 */
class ExecuteSymfonyType extends ExecuteTypeBase
{
    /**
     * {@inheritDoc}
     */
    public function createTaskInstance(): BaseTask
    {
        $command = $this->findCommand($this->getCommand());
        $taskInstance = (new SymfonyCommand($command));

        foreach ($this->getArguments() as $argument) {
            foreach ($argument as $name => $value) {
                $taskInstance->arg($name, $value);
            }
        }

        foreach ($this->getOptions() as $option => $value) {
            $taskInstance->opt($option, $value);
        }

        return $taskInstance;
    }

    /**
     * Find the symfony command instance.
     *
     * @param $name
     *   The symfony command name.
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function findCommand($name): Command
    {
        return $this->getApplication()->find($name);
    }

    /**
     * Get the project-x application instance.
     *
     * @return \Pr0jectX\Px\PxApp
     */
    protected function getApplication(): PxApp
    {
        return PxApp::service('application');
    }
}
