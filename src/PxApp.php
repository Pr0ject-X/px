<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use Pr0jectX\Px\Commands\Artifact;
use Pr0jectX\Px\Commands\Config;
use Pr0jectX\Px\Commands\Core;
use Pr0jectX\Px\Commands\Workflow;
use Pr0jectX\Px\ExecuteType\ExecuteTypeManager;
use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginCommandRegisterInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginCommandTaskBase;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Pr0jectX\Px\Workflow\WorkflowManager;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\IOAwareInterface;
use Robo\Robo;
use Robo\Runner;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Define the project-x console application.
 */
class PxApp extends Application
{
    /** @var string  */
    public const CONFIG_FILENAME = 'project-x';

    /** @var string  */
    protected const APPLICATION_NAME = 'Project-X';

    /** @var string  */
    protected const PLUGIN_NAMESPACE = 'ProjectX\Plugin';

    /**
     * @var \League\Container\ContainerInterface
     */
    protected static $container;

    /**
     * @var array
     */
    protected static $projectComposer;

    /**
     * @var string
     */
    protected static $projectRootPath;

    /**
     * @var string
     */
    protected static $projectSearchPath;

    /**
     * @var EnvironmentTypeInterface
     */
    protected static $projectEnvironment;

    /**
     * Define the project-x constructor.
     */
    public function __construct()
    {
        parent::__construct(static::displayBanner(), static::displayVersion());
    }

    /**
     * Display the project-x name.
     *
     * @return string
     *   The project-x application name.
     */
    public static function displayBanner(): string
    {
        $filename = dirname(__DIR__) . '/banner.txt';

        return file_exists($filename)
            ? file_get_contents($filename)
            : static::APPLICATION_NAME;
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
     * Set the project search path.
     *
     * @param string $searchPath
     *   The project search path to determine the root.
     */
    public static function setProjectSearchPath(string $searchPath): void
    {
        static::$projectSearchPath = getcwd();

        if (file_exists($searchPath)) {
            static::$projectSearchPath = $searchPath;
        }
    }

    /**
     * Determine if the project composer.json has been loaded.
     *
     * @return bool
     *   Return true if composer has been loaded; otherwise false.
     */
    public static function hasProjectComposer(): bool
    {
        return !empty(static::$projectComposer);
    }

    /**
     * Load the composer.json relevant to the project root.
     *
     * @throws \RuntimeException
     */
    public static function loadProjectComposer(): void
    {
        $composerFile = static::projectRootPath() . '/composer.json';

        if (!file_exists($composerFile)) {
            throw new \RuntimeException(
                'Unable to locate the composer.json within the project.'
            );
        }

        static::$projectComposer = json_decode(
            file_get_contents($composerFile),
            true
        );
    }

    /**
     * Load a service from the container by an identifier.
     *
     * @param string $identifier
     *   The container service identifier.
     *
     * @return mixed
     *   The instantiated service from the container.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function service(string $identifier)
    {
        return static::$container->get($identifier);
    }

    /**
     * Create the project-x dependency injection container.
     *
     * @param \Symfony\Component\Console\Input\InputInterface|null $input
     *   The console input stream.
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     *   The console output stream.
     * @param \Symfony\Component\Console\Application $app
     *   The console application.
     * @param $classLoader
     *   The console class loader.
     *
     * @return \League\Container\ContainerInterface
     *   The instantiated dependency injection container.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function createContainer(
        InputInterface $input = null,
        OutputInterface $output = null,
        Application $app,
        $classLoader
    ) {
        if (!static::hasContainer()) {
            $container = new Container();

            $config = Robo::createConfiguration(
                PxApp::configPaths()
            );

            Robo::configureContainer($container, $app, $config, $input, $output, $classLoader);

            $container->add('httpClient', function () {
                return HttpClient::create();
            });

            $container->share('workflowManager', WorkflowManager::class)
                ->withArguments([
                    'config', 'relativeNamespaceDiscovery'
                ]);

            $container->share('executeTypeManager', ExecuteTypeManager::class);

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
     * Get the project-x container.
     *
     * @return \League\Container\ContainerInterface
     *   The project-x service container.
     */
    public static function getContainer(): ContainerInterface
    {
        return static::$container;
    }

    /**
     * Determine if the project-x contain exist.
     *
     * @return bool
     *   Return true if the container exist and is valid; otherwise false.
     */
    public static function hasContainer(): bool
    {
        return isset(static::$container)
            && static::$container instanceof ContainerInterface;
    }

    /**
     * Define the project user directory.
     *
     * @return string
     *   The path to the user directory.
     */
    public static function userDir(): string
    {
        $userDirectory = (string) getenv('PX_USER_DIR');

        return (string) is_dir($userDirectory)
            ? $userDirectory
            : getenv('HOME');
    }

    /**
     * Define the project user shell.
     *
     * @return string
     *   The user current shell.
     */
    public static function userShell(): string
    {
        $userShell = (string) getenv('PX_USER_SHELL');
        $userShell = !empty($userShell)
            ? $userShell
            : getenv('SHELL');

        return substr(
            $userShell,
            strrpos($userShell, '/') + 1
        ) ?: '';
    }

    /**
     * Define the project global temporary directory.
     *
     * @return string
     *   The fully qualified path to the temporary directory.
     */
    public static function globalTempDir(): string
    {
        $userDirectory = static::userDir();
        return "{$userDirectory}/.project-x";
    }

    /**
     * Define the project temporary directory.
     *
     * @return string
     *   The fully qualified path to the project temporary directory.
     */
    public static function projectTempDir(): string
    {
        return static::projectRootPath() . '/.project-x';
    }

    /**
     * Define the project cache directory.
     *
     * @return string
     *   The fully qualified path to the project cache directory.
     */
    public static function projectCacheDir(): string
    {
        return implode(DIRECTORY_SEPARATOR, [PxApp::projectTempDir(), 'cache']);
    }

    /**
     * Retrieve the project default filesystem cache instance.
     *
     * @param string $namespace
     *   The cache namespace.
     * @param int $defaultLifetime
     *   The default expiration time.
     *
     * @return \Symfony\Component\Cache\Adapter\FilesystemAdapter
     */
    public static function projectCache(
        string $namespace = '',
        int $defaultLifetime = 0
    ): FilesystemAdapter {
        return new FilesystemAdapter(
            $namespace,
            $defaultLifetime,
            PxApp::projectCacheDir()
        );
    }

    /**
     * Define the active PHP versions.
     *
     * @return array
     *   An array of active PHP versions.
     */
    public static function activePhpVersions(): array
    {
        $versions = [];

        $activeVersions = json_decode(
            file_get_contents(
                'https://www.php.net/releases/active.php'
            ),
            true
        );

        foreach ($activeVersions as $activeVersion) {
            $versions = array_merge(
                $versions,
                array_keys($activeVersion)
            );
        }

        return $versions;
    }

    /**
     * Define the project root path.
     *
     * @return string
     *   The path to the project root.
     */
    public static function projectRootPath(): string
    {
        if (!isset(static::$projectRootPath)) {
            static::$projectRootPath = static::findFileRootPath(
                'composer.json'
            );
        }

        return static::$projectRootPath;
    }

    /**
     * Get the project environment type.
     *
     * @return string
     *   The current project environment type.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getEnvironmentType(): string
    {
        $configuration = static::getConfiguration();

        if ($configuration->has('environment')) {
            return (string) $configuration->get('environment');
        }

        // The plugin environment type has been removed from the directive. It
        // only needs to be kept as a fallback to support older versions.
        if ($configuration->has('plugins.environment.type')) {
            return (string) $configuration->get('plugins.environment.type');
        }

        return 'localhost';
    }

    /**
     * The project environment instance.
     *
     * @param array $defaultConfig
     *   An array of default configurations.
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentTypeInterface
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getEnvironmentInstance(array $defaultConfig = []): EnvironmentTypeInterface
    {
        if (!isset(static::$projectEnvironment)) {
            /** @var \Pr0jectX\Px\PluginManagerInterface $envManager */
            $envManager = static::service('environmentTypePluginManager');
            $config = static::getConfiguration()
                ->get('plugins.environment', $defaultConfig);

            static::$projectEnvironment = $envManager->createInstance(
                static::getEnvironmentType(),
                $config
            );
        }

        return static::$projectEnvironment;
    }

    /**
     * Get the project configuration instance.
     *
     * @return \Robo\Config\Config
     *   The project configuration instance.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getConfiguration(): \Robo\Config\Config
    {
        return static::service('config');
    }

    /**
     * Get the project composer definitions.
     *
     * @return array
     *   An array of composer.json definitions.
     */
    public static function getProjectComposer(): array
    {
        return static::$projectComposer;
    }

    /**
     * Check if a package is defined in the project composer.json.
     *
     * @param string $package
     *   The composer package name.
     *
     * @return bool
     *   Return true if composer package exist; otherwise false.
     */
    public static function composerHasPackage(string $package): bool
    {
        $composer = static::getProjectComposer();

        foreach (['require', 'require-dev'] as $section) {
            if (isset($composer[$section][$package])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load project-x related templates.
     *
     * @param string $filename
     *   The template filename.
     * @param array $directories
     *   An array of directories to append.
     *
     * @return string|null
     *   The template file contents.
     */
    public static function loadTemplate(
        string $filename,
        array $directories = []
    ): ?string {
        $filepath = implode(DIRECTORY_SEPARATOR, array_merge([
            APPLICATION_ROOT,
            'templates'
        ], $directories, [$filename]));

        if (!file_exists($filepath)) {
            return null;
        }

        return file_get_contents($filepath);
    }

    /**
     * Execute the project-x console application.
     *
     * @return int
     *   The project-x application status code.
     */
    public function execute(): int
    {
        $runner = (new Runner())
            ->setContainer(static::getContainer())
            ->setRelativePluginNamespace(static::PLUGIN_NAMESPACE);

        return $runner->run(
            static::service('input'),
            static::service('output'),
            $this,
            static::projectCommandClasses()
        );
    }

    /**
     * Define the project-x configuration paths.
     *
     * @return array
     *   An array of configuration paths.
     */
    protected static function configPaths(): array
    {
        $filename = static::CONFIG_FILENAME;
        $rootPath = static::projectRootPath();

        return [
            "{$rootPath}/{$filename}.yml",
            "{$rootPath}/{$filename}.local.yml",
        ];
    }

    /**
     * Get the project command classes that were discovered.
     *
     * @return array
     *   An array of discovered command classes.
     */
    protected static function projectCommandClasses(): array
    {
        return array_merge(
            static::coreCommandClasses(),
            static::pluginCommandClasses()
        );
    }

    /**
     * Define the project-x command classes.
     *
     * @return array
     *   An array of core command classes.
     */
    protected static function coreCommandClasses(): array
    {
        return [
            Core::class,
            Config::class,
            Artifact::class
        ];
    }

    /**
     * Get the plugin command classes.
     *
     * @return array
     *   An array of plugin command classes.
     */
    protected static function pluginCommandClasses(): array
    {
        $classes = [];

        $environment = static::getEnvironmentInstance();

        $classes = array_merge(
            $classes,
            static::resolvePluginCommandClasses($environment)
        );

        foreach (['commandTypePluginManager'] as $id) {
            $classes = array_merge($classes, ...static::discoverPluginManagerCommands(
                static::service($id)
            ));
        }

        return $classes;
    }

    /**
     * Create the plugin command instance.
     *
     * @param \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $plugin
     *   The plugin instance.
     * @param string $classname
     *   The command classname.
     *
     * @return \Pr0jectX\Px\CommandTasksBase
     */
    protected static function createPluginCommandInstance(
        PluginInterface $plugin,
        string $classname
    ): CommandTasksBase {
        $instance = is_subclass_of($classname, PluginCommandTaskBase::class)
            ? new $classname($plugin)
            : new $classname();

        if ($instance instanceof IOAwareInterface) {
            $instance->setInput(PxApp::service('input'));
            $instance->setOutput(PxApp::service('output'));
        }

        if ($instance instanceof ContainerAwareInterface) {
            $instance->setContainer(PxApp::getContainer());
        }

        if ($instance instanceof BuilderAwareInterface) {
            $instance->setBuilder(
                CollectionBuilder::create(PxApp::getContainer(), $instance)
            );
        }

        return $instance;
    }

    /**
     * Resolve plugin instance registered command classes.
     *
     * @param \Pr0jectX\Px\ProjectX\Plugin\PluginInterface $plugin
     *   The plugin instance.
     *
     * @return array
     *   An array of registered command commands for the plugin instance.
     */
    protected static function resolvePluginCommandClasses(
        PluginInterface $plugin
    ): array {
        $classes = [];

        if ($plugin instanceof PluginCommandRegisterInterface) {
            foreach ($plugin->registeredCommands() as $command) {
                if (
                    !class_exists($command)
                    || !is_subclass_of($command, CommandTasksBase::class)
                ) {
                    continue;
                }
                $classes[] = static::createPluginCommandInstance($plugin, $command);
            }
        }

        return $classes;
    }

    /**
     * Discover plugin manager registered command classes.
     *
     * @param \Pr0jectX\Px\PluginManagerInterface $plugin_manager
     *   The plugin manager instance.
     *
     * @return array
     *   An array of registered command classes based on the plugin manager.
     */
    protected static function discoverPluginManagerCommands(
        PluginManagerInterface $plugin_manager
    ): array {
        $commands = [];
        $interface = PluginCommandRegisterInterface::class;
        $configurations = PxApp::getConfiguration()->get('plugins') ?? [];

        foreach ($plugin_manager->loadInstancesWithInterface($interface, $configurations) as $pluginInstance) {
            $commands[] = static::resolvePluginCommandClasses($pluginInstance);
        }

        return $commands;
    }

    /**
     * Find the file root path.
     *
     * @param string $filename
     *   The search filename to base the path from.
     *
     * @return string
     *   The file root path; otherwise fallback to the provided search path.
     */
    protected static function findFileRootPath(string $filename): string
    {
        $searchPath = static::$projectSearchPath
            ?? getcwd();

        if (file_exists("{$searchPath}/{$filename}")) {
            return $searchPath;
        }
        $searchDirs = explode('/', $searchPath);
        $searchDirCount = count($searchDirs);

        for ($i = 1; $i < $searchDirCount - 1; $i++) {
            $searchDir = implode('/', array_slice($searchDirs, 0, -$i));

            if (file_exists("{$searchDir}/{$filename}")) {
                return $searchDir;
            }
        }

        return $searchPath;
    }
}
