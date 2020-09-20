<?php

declare(strict_types=1);

namespace Pr0jectX\Px\HookExecute;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\Config\ConfigInterface;
use Pr0jectX\Px\ExecuteType\ExecuteTypeManager;
use Robo\Collection\CollectionBuilder;
use Robo\Result;

/**
 * Define the hook execute manager class.
 */
class HookExecuteManager
{
    /**
     * @var \Consolidation\Config\ConfigInterface
     */
    protected $config;

    /**
     * @var \Consolidation\AnnotatedCommand\CommandData
     */
    protected $commandData;

    /**
     * @var array
     */
    protected $executeTypes = [];

    /**
     * @var \Pr0jectX\Px\ExecuteType\ExecuteTypeManager
     */
    protected $executeTypeManager;

    /**
     * Define the hook execute manager constructor.
     *
     * @param \Consolidation\Config\ConfigInterface $config
     *   The configuration instance.
     * @param \Consolidation\AnnotatedCommand\CommandData $commandData
     *   The command data instance.
     * @param \Pr0jectX\Px\ExecuteType\ExecuteTypeManager $executeTypeManager
     *   The execute type manager instance.
     */
    public function __construct(
        ConfigInterface $config,
        CommandData $commandData,
        ExecuteTypeManager $executeTypeManager
    ) {
        $this->config = $config;
        $this->commandData = $commandData;
        $this->executeTypeManager = $executeTypeManager;
    }

    /**
     * Execute the hook commands.
     *
     * @param string $hookType
     *   The hook type.
     * @param \Robo\Collection\CollectionBuilder $collection
     *   The collection builder instance.
     *
     * @return \Robo\Result
     *   The result instance.
     */
    public function executeCommands(
        string $hookType,
        CollectionBuilder $collection
    ): Result {
        $hookConfigs = $this->loadCommandHookConfigs()[$hookType]
            ?? [];

        return $this->executeTypeManager
            ->executeInstances(
                $this->buildCommands($hookConfigs),
                $collection
            );
    }

    /**
     * Build the execute type command.
     *
     * @param array $hookConfigs
     *   An array of hook configurations.
     *
     * @return array|\Pr0jectX\Px\Contracts\ExecuteTypeInterface[]
     *   An array of execute type commands.
     */
    protected function buildCommands(
        array $hookConfigs
    ): array {
        $commands = [];

        foreach ($hookConfigs as $hookConfig) {
            $hookConfig = is_string($hookConfig)
                ? ['type' => 'shell', 'command' => $hookConfig]
                : $hookConfig;

            if (!isset($hookConfig['type'])) {
                continue;
            }

            $commands[] = $this
                ->executeTypeManager
                ->createInstance($hookConfig['type'], $hookConfig);
        }

        return $commands;
    }

    /**
     * Load the command hook configurations.
     *
     * @return array
     *   Load the configs for the following hook command.
     */
    protected function loadCommandHookConfigs(): array
    {
        return $this->config->get(
            "hook.{$this->getCommandNameConfigKey()}",
            []
        );
    }

    /**
     * Get the hook command name.
     *
     * @return string
     *   The hook command name.
     */
    protected function getCommandName(): string
    {
        return $this->commandData->annotationData()->get('command');
    }

    /**
     * Get the hook command config key.
     *
     * @return string
     *   The hook command config key.
     */
    protected function getCommandNameConfigKey(): string
    {
        return implode('.', explode(':', $this->getCommandName()));
    }
}
