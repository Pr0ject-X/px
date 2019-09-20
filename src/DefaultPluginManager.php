<?php

namespace Droath\ProjectX;

use Droath\ProjectX\Exception\PluginNotFoundException;
use Droath\ProjectX\ProjectX\Plugin\PluginInterface;
use League\Container\ContainerAwareInterface;
use Robo\ClassDiscovery\ClassDiscoveryInterface;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\IOAwareInterface;

/**
 * Define the default plugin manager.
 */
abstract class DefaultPluginManager implements PluginManagerInterface
{
    /**
     * @var \Robo\ClassDiscovery\ClassDiscoveryInterface
     */
    protected $classDiscovery;

    /**
     * Define the default plugin manager constructor.
     *
     * @param \Robo\ClassDiscovery\ClassDiscoveryInterface $class_discovery
     *   The class discovery service.
     */
    public function __construct(ClassDiscoveryInterface $class_discovery)
    {
        $this->classDiscovery = $class_discovery;
    }

    /**
     * {@inheritDoc}
     */
    public function getClassname($plugin_id)
    {
        return $this->findPluginById(
            $plugin_id,
            $this->discoverPluginClasses()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        $options = [];

        /** @var PluginInterface $className */
        foreach ($this->discoverPluginClasses() as $className) {
            if (!is_subclass_of($className, PluginInterface::class)) {
                continue;
            }
            $options[$className::pluginId()] = $className::pluginLabel();
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($plugin_id, array $configurations = [])
    {
        $class_name = $this->getClassname($plugin_id);

        if (!$class_name) {
            throw new PluginNotFoundException($plugin_id);
        }
        /** @var PluginInterface $instance */
        $instance = new $class_name($configurations);

        $container = PxApp::getContainer();

        if ($instance instanceof IOAwareInterface) {
            $instance->setInput($container->get('input'));
            $instance->setOutput($container->get('output'));
        }

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

    /**
     * Find plugin by identifier.
     *
     * @param $plugin_id
     *   The plugin identifier.
     * @param array $plugins
     *   An array of plugins.
     *
     * @return bool|string
     *   Return a plugin class name; otherwise false if not found.
     */
    protected function findPluginById($plugin_id, array $plugins) {
        $interface = PluginInterface::class;

        /** @var \Droath\ProjectX\PluginInterface $class_name */
        foreach ($plugins as $class_name) {
            if (!class_exists($class_name)
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
