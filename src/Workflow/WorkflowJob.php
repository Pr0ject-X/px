<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Workflow;

use Pr0jectX\Px\Contracts\ExecuteTypeInterface;
use Pr0jectX\Px\PxApp;

/**
 * Define the project-x workflow job class.
 */
class WorkflowJob
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $commands;

    /**
     * @var array
     */
    protected $definition;

    /**
     * The workflow job constructor.
     *
     * @param string $name
     *   The workflow job machine name.
     * @param array $definition
     *   The workflow job definition.
     */
    public function __construct(
        string $name,
        array $definition
    ) {
        $this->name = $name;
        $this->definition = $definition;
    }

    /**
     * Get workflow job name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get workflow job label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->definition['label'];
    }

    /**
     * Get workflow job execute type instances.
     *
     * @return \Pr0jectX\Px\Contracts\ExecuteTypeInterface[]
     *   An array of execute type instances.
     */
    public function getCommandExecuteTypes(): array
    {
        $executeTypes = [];

        foreach ($this->getCommands() as $command) {
            $executeTypes[] = $command instanceof ExecuteTypeInterface
                ? $command
                : $this->createExecuteTypeInstance($command);
        }

        return $executeTypes;
    }

    /**
     * Get workflow job commands.
     *
     * @return array
     */
    protected function getCommands(): array
    {
        return $this->definition['commands'] ?? [];
    }

    /**
     * Create the execute type instance.
     *
     * @param $definition
     *   An array of the execute type definition.
     *
     * @return \Pr0jectX\Px\Contracts\ExecuteTypeInterface
     *   The execute type instance.
     */
    protected function createExecuteTypeInstance(
        array $definition
    ): ExecuteTypeInterface {
        /** @var \Pr0jectX\Px\ExecuteType\ExecuteTypeManager $executeTypeManager */
        $executeTypeManager = PxApp::service('executeTypeManager');

        $type = $definition['type'];
        unset($definition['type']);

        return $executeTypeManager->createInstance($type, $definition);
    }
}
