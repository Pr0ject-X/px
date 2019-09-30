<?php

namespace Pr0jectX\Px;

use Composer\Autoload\ClassLoader;
use Pr0jectX\Px\Commands\Artifact;
use Pr0jectX\Px\Commands\Config;
use Pr0jectX\Px\Commands\Core;
use Pr0jectX\Px\Commands\Environment;
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
     * @var \League\Container\ContainerInterface
     */
    public static $container;

    /**
     * @var \Robo\Config\Config
     */
    public static $config;

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
        parent::__construct($this->printBanner(), $this->printVersion());

        $this->input = $input;
        $this->output = $output;

        static::$container = Robo::createDefaultContainer(
            $input,
            $output,
            $this,
            static::getConfiguration(),
            $classloader
        );

        static::setContainer();
    }

    /**
     * Get the project root path.
     *
     * @return bool|string
     *   The project root path; otherwise false if not found.
     */
    public static function projectRootPath()
    {
        return static::findFileRootPath('composer.json');
    }

    /**
     * Get the project-x container.
     *
     * @return \League\Container\Container|\League\Container\ContainerInterface
     *   The project-x service container.
     */
    public static function getContainer() {
        return static::$container;
    }

    /**
     * Set the container with project-x services.
     */
    public static function setContainer()
    {
        $container = static::$container;

        $container->share('deployTypePluginManager', \Pr0jectX\Px\DeployTypePluginManager::class)
            ->withArgument('relativeNamespaceDiscovery');
        $container->share('environmentTypePluginManager', \Pr0jectX\Px\EnvironmentTypePluginManager::class)
            ->withArgument('relativeNamespaceDiscovery');
    }

    /**
     * Define the project-x core command classes.
     *
     * @return array
     *   An array of core command classes.
     */
    public static function coreCommandClasses()
    {
        return [
            Core::class,
            Artifact::class,
        ];
    }

    /**
     * Load the service by identifier.
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
     * Define the project-x configuration paths.
     *
     * @return array
     *   An array of configuration paths.
     */
    public static function configPaths() {
        return [
            'project-x.yml',
            'project-x.local.yml',
        ];
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
     * Create project-x configuration.
     *
     * @return \Robo\Config\Config
     *   The newly created project-x configuration instance.
     */
    public static function createConfiguration()
    {
        static::$config = Robo::createConfiguration(static::configPaths());

        return static::$config;
    }

    /**
     * Get the project-x application input.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * Get the project-x application output.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function output()
    {
        return $this->output;
    }

    /**
     * Execute the project-x application.
     *
     * @return int
     *   The project-x application status code.
     */
    public function execute() {
        $runner = (new Runner())
            ->setContainer($this->getContainer())
            ->setRelativePluginNamespace(static::PLUGIN_NAMESPACE);

        return $runner->run(
            $this->input(), $this->output(), $this, static::coreCommandClasses()
        );
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
        if (!isset($search_path) || file_exists($search_path)) {
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

    /**
     * Print application version.
     *
     * @return false|string
     */
    private function printVersion()
    {
        return file_get_contents(
            dirname(__DIR__) . '/VERSION'
        );
    }

    /**
     * Print application banner.
     *
     * @return false|string
     */
    private function printBanner()
    {
        $filename = dirname(__DIR__) . '/banner.txt';

        if (!file_exists($filename)) {
          return static::APPLICATION_NAME;
        }

        return file_get_contents($filename);
    }
}
