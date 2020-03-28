<?php

declare(strict_types=1);

namespace Pr0jectX\Px\HookExecuteType;

use Pr0jectX\Px\PxApp;
use Robo\Task\Base\SymfonyCommand;
use Symfony\Component\Console\Command\Command;

/**
 * Define the execute symfony type.
 */
class ExecuteSymfonyType extends ExecuteTypeBase implements ExecuteTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function type(): string
    {
        return 'symfony';
    }

    /**
     * @inheritDoc
     */
    public function build(array $config): array
    {
        if (!isset($config['command'])) {
            throw new \InvalidArgumentException(
                'The command property is required!'
            );
        }

        return [
            'command' => $this->buildCommand(
                $config['command']
            ),
            'options' => $config['options'] ?? [],
            'arguments' => $config['arguments'] ?? [],
            'classname' => SymfonyCommand::class,
        ];
    }

    /**
     * Build the symfony command.
     *
     * @param string $name
     *   The symfony command name.
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function buildCommand(
        string $name
    ): Command {
        if ($command = $this->findApplicationCommand($name)) {
            return $command;
        }

        throw new \RuntimeException(
            sprintf('Unable to locate the %s command', $name)
        );
    }

    /**
     * Find the application command instance.
     *
     * @param $name
     *   The application command name to search.
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function findApplicationCommand($name): Command
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
