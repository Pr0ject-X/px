<?php

declare(strict_types=1);

namespace Pr0jectX\Px\HookExecuteType;

use Consolidation\AnnotatedCommand\CommandData;
use Pr0jectX\Px\PxApp;
use Robo\Collection\CollectionBuilder;
use Robo\Result;
use Robo\Task\Base\Exec;
use Robo\Task\Base\SymfonyCommand;
use Robo\Task\BaseTask;

trait ExecuteHookTaskTrait
{
    /**
     * Execute the hook collection tasks.
     *
     * @param \Consolidation\AnnotatedCommand\CommandData $commandData
     *   The command data instance.
     * @param string $hookType
     *   The hook type, such as (pre, post, etc)
     *
     * @return \Robo\Result
     */
    protected function executeHookCollectionTasks(
        CommandData $commandData,
        string $hookType
    ): Result {
        $config = PxApp::getConfiguration();

        $hookCommands = (new HookExecuteManager(
            $config,
            $commandData,
            $this->collectionBuilder()
        ))->buildCommands($hookType);

        $collection = $this->collectionBuilder();

        foreach ($hookCommands as $hookCommand) {
            $collection->addTask(
                $this->buildHookTask($hookCommand)
            );
        }

        return $collection->run();
    }

    /**
     * Build the task based on the hook command information.
     *
     * @param array $hookCommand
     *   An array of hook command configurations.
     *
     * @return \Robo\Collection\CollectionBuilder
     */
    protected function buildHookTask(array $hookCommand): CollectionBuilder
    {
        if (
            !isset($hookCommand['classname'])
            || !is_subclass_of($hookCommand['classname'], BaseTask::class)
        ) {
            throw new \InvalidArgumentException(
                sprintf('The %s classname is invalid!', $hookCommand['classname'])
            );
        }
        $hookOptions = $this->normalizeCommandOptions(
            $hookCommand['options']
        );
        $hookArguments = $hookCommand['arguments'];
        $hookClassname = $hookCommand['classname'];
        $hookClassTask = $this->task($hookClassname, $hookCommand['command']);

        switch ($hookClassname) {
            case Exec::class:
                $hookClassTask->options($hookOptions);
                if (!$this->commandArgumentIsValid($hookArguments)) {
                    throw new \RuntimeException(
                        'The hook command arguments are invalid!'
                    );
                }
                $hookClassTask->args($hookArguments);
                break;
            case SymfonyCommand::class:
                foreach ($hookOptions as $option => $value) {
                    $hookClassTask->opt($option, $value);
                }
                foreach ($hookArguments as $argument) {
                    foreach ($argument as $name => $value) {
                        $hookClassTask->arg($name, $value);
                    }
                }
                break;
        }

        return $hookClassTask;
    }

    /**
     * Normalize the command options.
     *
     * @param array $options
     *   An array of command options.
     *
     * @return array
     *   An array of normalized command options that support key/value options.
     */
    protected function normalizeCommandOptions(array $options): array
    {
        $commandOptions = [];

        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $commandOptions[$value] = null;
            } else {
                $commandOptions[$key] = $value;
            }
        }

        return $commandOptions;
    }

    /**
     * Check if the command argument is valid.
     *
     * @param array $arguments
     *   An array of command arguments.
     *
     * @return bool
     *   Return true if command arguments are valid; otherwise false.
     */
    protected function commandArgumentIsValid(array $arguments): bool
    {
        foreach ($arguments as $key => $value) {
            if (is_array($value)) {
                return false;
            }
        }

        return true;
    }
}
