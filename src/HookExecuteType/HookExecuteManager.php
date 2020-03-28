<?php

declare(strict_types=1);

namespace Pr0jectX\Px\HookExecuteType;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\Config\ConfigInterface;

/**
 * Define the hook execute manager.
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
     * Define the hook execute manager constructor.
     *
     * @param \Consolidation\Config\ConfigInterface $config
     *   The configuration instance.
     * @param \Consolidation\AnnotatedCommand\CommandData $commandData
     *   The command data instance.
     */
    public function __construct(
        ConfigInterface $config,
        CommandData $commandData
    ) {
        $this->config = $config;
        $this->commandData = $commandData;
    }

    /**
     * Run the execute type command.
     *
     * @param $hookType
     *   The hook type that's being invoked.
     * @return array
     */
    public function buildCommands(string $hookType): array
    {
        $commands = [];
        $executeTypes = $this->loadExecuteTypes();
        $hookConfigs = $this->loadCommandHookConfigs()[$hookType] ?? [];

        foreach ($hookConfigs as $hookConfig) {
            $hookConfig = is_string($hookConfig)
                ? ['type' => 'shell', 'command' => $hookConfig]
                : $hookConfig;

            if (
                !isset($hookConfig['type'])
                || !in_array($hookConfig['type'], array_keys($executeTypes))
            ) {
                continue;
            }
            /** @var \Pr0jectX\Px\HookExecuteType\ExecuteTypeInterface $instance */
            $instance = $executeTypes[$hookConfig['type']];
            $commands[] = $instance->build($hookConfig);
        }

        return $commands;
    }

    /**
     * Load execute type instances.
     *
     * @return array
     *   An array of execute type instances.
     */
    protected function loadExecuteTypes(): array
    {
        if (empty($this->executeTypes)) {
            foreach ($this->registerExecuteTypes() as $classname) {
                if (
                    !class_exists($classname)
                    || !is_subclass_of($classname, ExecuteTypeInterface::class)
                ) {
                    continue;
                }
                $instance = new $classname();
                $this->executeTypes[$instance->type()] = $instance;
            }
        }

        return $this->executeTypes;
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

    /**
     * Registered execute types.
     *
     * @return array
     *   An array of registered execute types.
     */
    protected function registerExecuteTypes(): array
    {
        return [
            ExecuteSymfonyType::class,
            ExecuteShellType::class,
        ];
    }
}
