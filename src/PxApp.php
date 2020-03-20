<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Composer\Autoload\ClassLoader;
use Consolidation\Config\ConfigInterface;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use Pr0jectX\Px\Commands\Artifact;
use Pr0jectX\Px\Commands\Config;
use Pr0jectX\Px\Commands\Core;
use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginCommandRegisterInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginCommandTaskBase;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\IOAwareInterface;
use Robo\Robo;
use Robo\Runner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Define the project-x console application.
 */
class PxApp extends Application
{
    /** @var string  */
    const CONFIG_FILENAME = 'project-x';

    /** @var string  */
    const APPLICATION_NAME = 'Project-X';

    /** @var string  */
    const PLUGIN_NAMESPACE = 'ProjectX\Plugin';

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \Robo\Config\Config
     */
    protected static $config;

    /**
     * @var \League\Container\ContainerInterface
     */
    protected static $container;

    /**
     * @var array
     */
    protected static $projectComposer;

    /**
     * Define the project-x constructor.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The console input stream.
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The console output stream.
     * @param \Composer\Autoload\ClassLoader $classloader
     *   The composer class loader instance.
     */
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        ClassLoader $classloader = null
    ) {
        parent::__construct(static::displayBanner(), static::displayVersion());

        $this->input = $input;
        $this->output = $output;

        static::createContainer(
            $input,
            $output,
            $this,
            static::getConfiguration(),
            $classloader
        );
        static::setProjectComposer();
    }

    /**
     * Display the project-x name.
     *
     * @return string
     *   The project-x application name.
     */
    public static function displayBanner() : string
    {
        $filename = dirname(__DIR__) . '/banner.txt';

        if (!file_exists($filename)) {
            return static::APPLICATION_NAME;
        }

        return file_get_contents($filename) ?? static::APPLICATION_NAME;
    }

    /**
     * Display the project-x version.
     *
     * @return string
     *   The project-x version number.
     */
    public static function displayVersion()
    {
        return file_get_contents(dirname(__DIR__) . '/VERSION')
            ?? '0.0.0';
    }

    /**
     * Determine if hte contain exist.
     *
     * @return bool
     *   Return true if the container exist and is valid; otherwise false.
     */
    public static function hasContainer() : bool
    {
        return isset(static::$container)
            && static::$container instanceof ContainerInterface;
    }

    /**
     * Get the project-x container.
     *
     * @return \League\Container\ContainerInterface
     *   The project-x service container.
     */
    public static function getContainer() : ContainerInterface
    {
        return static::$container;
    }

    /**
     * Load the container service by identifier.
     *
     * @param $id
     *   The container service identifier.
     *
     * @return mixed
     *   The service from the container.
     */
    public static function service($id)
    {
        return static::$container->get($id);
    }

    /**
     * Define the project-x global temporary directory.
     *
     * @return string
     *   The fully qualified path to the global temporary directory.
     */
    public static function globalTempDir() : string
    {
        return "{$_SERVER['HOME']}/.project-x";
    }

    /**
     * Define the project-x temporary directory.
     *
     * @return string
     *   The fully qualified path to the project temporary directory.
     */
    public static function projectTempDir() : string
    {
        return static::projectRootPath() . '/.project-x';
    }

    /**
     * Get the project-x root path.
     *
     * @return bool|string
     *   The project root path; otherwise false if not found.
     */
    public static function projectRootPath() : string
    {
        return static::findFileRootPath('composer.json');
    }

    /**
     * Define the project-x command classes.
     *
     * @return array
     *   An array of core command classes.
     */
    public static function coreCommandClasses() : array
    {
        return array_merge([
            Core::class,
            Config::class,
            Artifact::class
        ], static::pluginCommandClasses());
    }

    /**
     * The project-x environment instance.
     *
     * @param array $config
     *   The configurations to pass along to the instance.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface
     */
    public static function getEnvironmentInstance(array $config = []) : EnvironmentTypeInterface
    {
        /** @var \Pr0jectX\Px\PluginManagerInterface $envManager */
        $envManager = static::service('environmentTypePluginManager');

        return $envManager->createInstance(
            static::getEnvironmentType(), $config
        );
    }

    /**
     * Get the plugin environment type.
     *
     * @return string
     *   The current plugin environment type.
     */
    public static function getEnvironmentType() : string
    {
        $configuration = static::getConfiguration();

        return $configuration->has('plugins.environment.type')
            ? (string) $configuration->get('plugins.environment.type')
            : 'localhost';
    }

    /**
     * Get project-x configuration.
     *
     * @return \Robo\Config\Config
     *   The project-x configuration instance.
     */
    public static function getConfiguration()
    {
        $config = static::$config;

        if (isset($config)) {
            return $config;
        }

        return static::createConfiguration();
    }

    /**
     * Get the project-x project composer.
     *
     * @return array
     *   An array of composer.json definitions.
     */
    public static function getProjectComposer() : array
    {
        return static::$projectComposer;
    }

    /**
     * Check if a package is defined in the project-x composer.json.
     *
     * @param string $package
     *   The composer package name.
     *
     * @return bool
     *   Return true if composer package exist; otherwise false.
     */
    public static function composerHasPackage(string $package) : bool
    {
        return isset(static::getProjectComposer()['require'][$package]);
    }

    /**
     * Get the project-x application input.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function input() : InputInterface
    {
        return $this->input;
    }

    /**
     * Get the project-x application output.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function output() : OutputInterface
    {
        return $this->output;
    }

    /**
     * Execute the project-x application.
     *
     * @return int
     *   The project-x application status code.
     */
    public function execute() : int
    {
        $runner = (new Runner())
            ->setContainer($this->getContainer())
            ->setRelativePluginNamespace(static::PLUGIN_NAMESPACE);

        return $runner->run(
            $this->input(), $this->output(), $this, static::coreCommandClasses()
        );
    }

    /**
     * Create the project-x dependency injection container.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The console input stream.
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The console output stream.
     * @param \Symfony\Component\Console\Application $app
     *   The console application.
     * @param \Consolidation\Config\ConfigInterface $config
     *   The console configuration.
     * @param $classLoader
     *   The console class loader.
     *
     * @return \League\Container\ContainerInterface
     *   The instantiated dependency injection container.
     */
    protected static function createContainer(
        InputInterface $input,
        OutputInterface $output,
        Application $app,
        ConfigInterface $config,
        $classLoader
    )
    {
        if (!static::hasContainer()) {
            $container = new Container();

            Robo::configureContainer($container, $app, $config, $input, $output, $classLoader);

            $container->share('deployTypePluginManager', DeployTypePluginManager::class)
                ->withArguments([
                    'input', 'output', 'relativeNamespaceDiscovery'
                ]);
            $container->share('commandTypePluginManager', CommandTypePluginManager::class)
                ->withArguments([
                    'input', 'output', 'relativeNamespaceDiscovery'
                ]);
            $container->share('environmentTypePluginManager', EnvironmentTypePluginManager::class)
                ->withArguments([
                    'input', 'output', 'relativeNamespaceDiscovery'
                ]);

            $app->setDispatcher($container->get('eventDispatcher'));

            static::$container = $container;
        }

        return static::$container;
    }

    /**
     * Set the project-x composer.json file contents.
     *
     * @throws \RuntimeException
     */
    protected static function setProjectComposer()
    {
        $composerFile = static::projectRootPath() . '/composer.json';

        if (!file_exists($composerFile)) {
            throw new \RuntimeException(
                'Unable to locate the composer.json within the project.'
            );
        }

        static::$projectComposer = json_decode(
            file_get_contents($composerFile), true
        );
    }

    /**
     * Define the project-x configuration paths.
     *
     * @return array
     *   An array of configuration paths.
     */
    protected static function configPaths() : array
    {
        $filename = static::CONFIG_FILENAME;
        return [
            "{$filename}.yml",
            "{$filename}.local.yml",
        ];
    }

    /**
     * Create project-x configuration.
     *
     * @return \Robo\Config\Config
     *   The newly created project-x configuration instance.
     */
    protected static function createConfiguration() : ConfigInterface
    {
        $config = new \Robo\Config\Config();

        Robo::loadConfiguration(
            static::configPaths(), $config
        );

        return $config;
    }

    /**
     * Get the plugin command classes.
     *
     * @return array
     *   An array of plugin command classes.
     */
    protected static function pluginCommandClasses() : array
    {
        $classes = [];

        $environment = static::getEnvironmentInstance();

        $classes = array_merge(
            $classes,
            static::discoverPluginCommandClasses($environment)
        );

        foreach (['commandTypePluginManager'] as $id) {
            $classes = array_merge($classes, ...static::discoverPluginManagerCommands(
                static::service($id)
            ));
        }

        return $classes;
    }

    /**
     * Get the instantiated plugin command instance.
     *
     * @param \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $plugin
     *   The plugin instance.
     * @param string $classname
     *   The command classname.
     *
     * @return \Pr0jectX\Px\CommandTasksBase
     */
    protected static function pluginCommandFactory(
        PluginInterface $plugin,
        string $classname
    ) : CommandTasksBase {
        if (is_subclass_of($classname, PluginCommandTaskBase::class)) {
            return new $classname($plugin);
        }

        return new $classname();
    }

    /**
     * Discover plugin instance registered command classes.
     *
     * @param \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $plugin
     *
     * @return array
     *   An array of registered command commands for the given plugin instance.
     */
    protected static function discoverPluginCommandClasses(
        PluginInterface $plugin
    ) : array {
        $classes = [];

        if ($plugin instanceof PluginCommandRegisterInterface) {
            foreach ($plugin->registeredCommands() as $command) {
                if (!class_exists($command)
                    || !is_subclass_of($command, CommandTasksBase::class)) {
                    continue;
                }
                $instance = static::pluginCommandFactory($plugin, $command);

                if ($instance instanceof IOAwareInterface) {
                    $instance->setInput(PxApp::service('input'));
                    $instance->setOutput(PxApp::service('output'));
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
                $classes[] = $instance;
            }
        }

        return $classes;
    }

    /**
     * Discover plugin manager registered command classes.
     *
     * @param \Pr0jectX\Px\PluginManagerInterface $plugin_manager
     *
     * @return array
     *   An array of registered command classes based on the plugin manager.
     */
    protected static function discoverPluginManagerCommands(
        PluginManagerInterface $plugin_manager
    ) : array {
        $commands = [];
        $interface = PluginCommandRegisterInterface::class;
        $configurations = PxApp::getConfiguration()->get('plugins') ?? [];

        foreach ($plugin_manager->loadInstancesWithInterface($interface, $configurations) as $pluginInstance) {
            $commands[] = static::discoverPluginCommandClasses($pluginInstance);
        }

        return $commands;
    }

    /**
     * Find root path for a given file name.
     *
     * @param $filename
     *   The file name.
     * @param string $search_path
     *   The directory search path.
     *
     * @return boolean|string
     *   The root path to the given file name.
     */
    protected static function findFileRootPath($filename, $search_path = NULL)
    {
        if (!isset($search_path) || !file_exists($search_path)) {
            $search_path = getcwd();
        }
        $paths = [];

        foreach (explode('/', $search_path) as $directory) {
            $paths[] = $directory;
            $root_path = implode('/', $paths);

            if (file_exists("{$root_path}/{$filename}")) {
                return $root_path;
            }
        }

        return false;
    }
}
