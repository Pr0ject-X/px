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
    protected $plugins = [];

    /**
     * @var \Robo\ClassDiscovery\ClassDiscoveryInterface
     */
    protected $classDiscovery;

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
        $this->classDiscovery = $class_discovery;
    }

    /**
     * {@inheritDoc}
     */
    public function getClassname(string $pluginId): string
    {
        if (empty($this->plugins)) {
            $this->getPlugins();
        }

        return $this->plugins[$pluginId] ?? "";
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(array $excludeIds = []): array
    {
        $options = [];

        /** @var \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $plugin */
        foreach ($this->getPlugins() as $pluginId => $pluginClass) {
            $options[$pluginId] = $pluginClass::pluginLabel();
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance(
        string $pluginId,
        array $configurations = []
    ): PluginInterface {
        $pluginArgHash = serialize($configurations);
        $pluginCacheId = sha1("{$pluginId}:{$pluginArgHash}");

        if (!isset(static::$pluginInstances[$pluginCacheId])) {
            static::$pluginInstances[$pluginCacheId] = $this->instantiatePluginInstance(
                $pluginId,
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
     *   An array of instantiated plugins that matched an interface.
     */
    public function loadInstancesWithInterface(
        string $interface,
        array $configurations = []
    ): array {
        $instances = [];

        foreach ($this->getPlugins() as $classname) {
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
     * Get the plugins that were discovered.
     *
     * @return array
     *   An array of plugin classes that were discovered.
     */
    protected function getPlugins(): array
    {
        if (empty($this->plugins)) {
            $classes = $this->discover($this->classDiscovery);

            /** @var \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $pluginClass */
            foreach ($classes as $pluginClass) {
                if (
                    !class_exists($pluginClass)
                    || !is_subclass_of($pluginClass, PluginInterface::class)
                ) {
                    continue;
                }
                $this->plugins[$pluginClass::pluginId()] = $pluginClass;
            }
        }

        return $this->plugins;
    }
}
