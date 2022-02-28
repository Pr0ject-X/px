<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\ProjectX\Plugin\PluginConfigurationBuilderInterface;
use Pr0jectX\Px\PxApp;

/**
 * Define the configuration command.
 */
class Config extends CommandTasksBase
{
    /**
     * Set the project plugin configuration.
     *
     * @param null $name
     *   The plugin configuration name.
     * @param array $opts
     * @option bool $hide-banner
     *   Hide banner from display.
     */
    public function configSet($name = null, array $opts = [
        'hide-banner' => false
    ]): void
    {
        if (isset($opts['hide-banner']) && !$opts['hide-banner']) {
            print PxApp::displayBanner();
        }

        try {
            $options = $this->configOptions();

            if (empty($options)) {
                throw new \RuntimeException(
                    'There are no plugins to configure.'
                );
            }

            if (!isset($name)) {
                $name = $this->askChoice(
                    'Set plugin configuration for',
                    $options
                );
            }

            if ($config = $this->buildPluginConfiguration($name)) {
                if ($this->savePluginConfiguration($config)) {
                    $this->success(
                        sprintf('The %s plugin configuration has successfully been saved.', $name)
                    );
                }
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Define the plugin configuration router.
     *
     * @return array
     *   An array of plugin configuration router details.
     */
    protected function configPluginRouter(): array
    {
        $router = [];
        $config = PxApp::getConfiguration();
        $interface = PluginConfigurationBuilderInterface::class;

        /** @var \Pr0jectX\Px\PluginManagerInterface $environmentManager */
        $environmentManager = PxApp::service('environmentTypePluginManager');
        $environmentConfigs = $config->get('plugins.environment', []);

        $environmentInstance = $environmentManager->createInstance(
            PxApp::getEnvironmentType(),
            $environmentConfigs
        );

        if (is_subclass_of($environmentInstance, $interface)) {
            $router['environment'] = [
                'class' => $environmentInstance
            ];
        }

        /** @var \Pr0jectX\Px\PluginManagerInterface $pluginManager */
        $pluginManager = PxApp::service('commandTypePluginManager');

        $pluginConfigurations = PxApp::getConfiguration()->get('plugins', []);

        /** @var \Pr0jectX\Px\ProjectX\Plugin\PluginConfigurationBuilderInterface $pluginInstance */
        foreach ($pluginManager->loadInstancesWithInterface($interface, $pluginConfigurations) as $pluginId => $pluginInstance) {
            $router[$pluginId]['class'] = $pluginInstance;
        }

        return $router;
    }

    /**
     * An array of plugin configuration options.
     *
     * @return array
     *   An array of plugin configuration options.
     */
    protected function configOptions(): array
    {
        $options = [];

        foreach (array_keys($this->configPluginRouter()) as $name) {
            $options[] = $name;
        }

        return $options;
    }

    /**
     * Build plugin configuration array.
     *
     * @param $name
     *   The configuration name.
     *
     * @return array
     *   An array of the build configuration.
     *
     * @throws \Exception
     */
    protected function buildPluginConfiguration($name): array
    {
        $config = [];

        if (isset($this->configPluginRouter()[$name])) {
            $pluginInstance = $this->configPluginRouter()[$name]['class'];

            if ($pluginInstance instanceof PluginConfigurationBuilderInterface) {
                $config[$name] = $pluginInstance->pluginConfiguration()->build();
            }
        } else {
            throw new \RuntimeException(
                sprintf('The configuration key "%s" is invalid!', $name)
            );
        }

        return $config;
    }

    /**
     * Save plugin configuration.
     *
     * @param array $plugins
     *   An array of the plugin configuration.
     *
     * @return bool
     *   Return true if the plugin configuration were saved; otherwise false.
     */
    protected function savePluginConfiguration(array $plugins): bool
    {
        return $this->writeConfiguration(['plugins' => $plugins]);
    }
}
