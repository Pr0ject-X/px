<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Pr0jectX\Px\Exception\PluginNotFoundException;
use League\Container\ContainerAwareInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Robo\ClassDiscovery\ClassDiscoveryInterface;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\IOAwareInterface;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

/**
 * Define the default plugin manager.
 */
abstract class DefaultPluginManager implements PluginManagerInterface
{
    /**
     * @var \Symfony\Component\Console\Input\Input
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\Output
     */
    protected $output;

    /**
     * @var array
     */
    protected $pluginClasses = [];

    /**
     * @var array
     */
    protected static $pluginInstances = [];

    /**
     * Define the default plugin manager constructor.
     *
     * @param \Symfony\Component\Console\Input\Input $input
     *   The symfony console input.
     * @param \Symfony\Component\Console\Output\Output $output
     *   The symfony console output.
     * @param \Robo\ClassDiscovery\ClassDiscoveryInterface $class_discovery
     *   The class discovery service.
     */
    public function __construct(
        Input $input,
        Output $output,
        ClassDiscoveryInterface $class_discovery
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->discover($class_discovery);
    }

    /**
     * {@inheritDoc}
     */
    public function getClassname(string $plugin_id): string
    {
        return $this->findPluginById(
            $plugin_id,
            $this->pluginClasses
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(array $exclude_ids = []): array
    {
        $options = [];

        /** @var PluginInterface $className */
        foreach ($this->pluginClasses as $className) {
            $pluginId = $className::pluginId();

            if (
                in_array($pluginId, $exclude_ids)
                || !is_subclass_of($className, PluginInterface::class)
            ) {
                continue;
            }
            $options[$pluginId] = $className::pluginLabel();
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance(
        string $plugin_id,
        array $configurations = []
    ): PluginInterface {
        $pluginArgHash = serialize($configurations);
        $pluginCacheId = sha1("{$plugin_id}:{$pluginArgHash}");

        if (!isset(static::$pluginInstances[$pluginCacheId])) {
            static::$pluginInstances[$pluginCacheId] = $this->instantiatePluginInstance(
                $plugin_id,
                $configurations
            );
        }

        return static::$pluginInstances[$pluginCacheId];
    }

    /**
     * Load plugin instance with interface.
     *
     * @param string $interface
     *   The fully qualified interface name.
     * @param array $configurations
     *   An array of plugin configurations keyed by the plugin id.
     *
     * @return array
     *   An array of plugin instances that match the defined interface.
     */
    public function loadInstancesWithInterface(
        string $interface,
        array $configurations = []
    ): array {
        $instances = [];

        foreach ($this->pluginClasses as $classname) {
            if (!is_subclass_of($classname, $interface)) {
                continue;
            }
            $pluginId = $classname::PluginId();
            $pluginConfig = $configurations[$pluginId] ?? [];

            $instances[$pluginId] = $this->createInstance(
                $pluginId,
                $pluginConfig
            );
        }

        return $instances;
    }

    /**
     * Instantiate plugin instance.
     *
     * @param string $plugin_id
     *   The plugin identifier.
     * @param array $configurations
     *   An array of plugin configurations.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\PluginInterface
     *
     * @throws \Pr0jectX\Px\Exception\PluginNotFoundException
     */
    protected function instantiatePluginInstance(
        string $plugin_id,
        array $configurations
    ): PluginInterface {
        if ($classname = $this->getClassname($plugin_id)) {

            /** @var \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $instance */
            $instance = new $classname($this, $configurations);

            if ($instance instanceof IOAwareInterface) {
                $instance->setInput($this->input);
                $instance->setOutput($this->output);
            }
            $container = PxApp::getContainer();

            if ($instance instanceof ContainerAwareInterface) {
                $instance->setContainer($container);
            }

            if ($instance instanceof BuilderAwareInterface) {
                $instance->setBuilder(
                    CollectionBuilder::create($container, $instance)
                );
            }

            return $instance;
        }

        throw new PluginNotFoundException($plugin_id);
    }

    /**
     * Find plugin by identifier.
     *
     * @param string $plugin_id
     *   The plugin identifier.
     * @param array $plugins
     *   An array of plugins.
     *
     * @return bool|string
     *   Return a plugin class name; otherwise false if not found.
     */
    protected function findPluginById(string $plugin_id, array $plugins)
    {
        $interface = PluginInterface::class;

        /** @var \Pr0jectX\Px\PluginInterface $class_name */
        foreach ($plugins as $class_name) {
            if (
                !class_exists($class_name)
                || !is_subclass_of($class_name, $interface)
                || $class_name::pluginId() !== $plugin_id
            ) {
                continue;
            }

            return $class_name;
        }

        return false;
    }
}
