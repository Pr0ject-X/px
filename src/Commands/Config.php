<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\ProjectX\Plugin\PluginConfigurationBuilderInterface;
use Pr0jectX\Px\PxApp;
use Symfony\Component\Yaml\Yaml;

/**
 * Define the configuration command.
 */
class Config extends CommandTasksBase
{
    /**
     * Set the project plugin configuration.
     *
     * @param $name
     *   The plugin configuration name.
     *
     * @throws \Exception
     */
    public function configSet($name = null)
    {
        print PxApp::displayBanner();

        if (!isset($name)) {
            $name = $this->choice(
                'Set plugin configuration for', $this->configOptions()
            );
        }

        if ($config = $this->buildPluginConfiguration($name)) {
           if ($this->savePluginConfiguration($config)) {
               $this->success(
                   sprintf('The %s plugin configuration has successfully been saved.', $name)
               );
           }
        }
    }

    /**
     * Define the plugin configuration router.
     *
     * @return array
     *   An array of plugin configuration router details.
     */
    protected function configPluginRouter() : array
    {
        $router = [
            'environment' => [
                'class' => PxApp::getEnvironmentInstance()
            ],
        ];
        $interface = PluginConfigurationBuilderInterface::class;

        /** @var \Pr0jectX\Px\PluginManagerInterface $pluginManager */
        $pluginManager = PxApp::service('commandTypePluginManager');

        /** @var \Pr0jectX\Px\ProjectX\Plugin\PluginConfigurationBuilderInterface $pluginInstance */
        foreach ($pluginManager->loadInstancesWithInterface($interface) as $pluginId => $pluginInstance) {
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
    protected function configOptions() : array
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
    protected function buildPluginConfiguration($name) : array
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
     * Save the plugin configurations.
     *
     * @param array $configurations
     *   An array of the plugin configurations.
     *
     * @return bool
     *   Return true if the configurations were saved; otherwise false.
     */
    protected function savePluginConfiguration(array $configurations) : bool
    {
        $projectRoot = PxApp::projectRootPath();
        $configFilename = PxApp::CONFIG_FILENAME;

        $pluginConfig = PxApp::getConfiguration()
            ->set('plugins', $configurations)
            ->export();

        $results = $this->taskWriteToFile("{$projectRoot}/{$configFilename}.yml")
            ->text(Yaml::dump($pluginConfig, 10, 4))
            ->run();

        return $results->wasSuccessful();
    }
}
