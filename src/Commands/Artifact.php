<?php

namespace Pr0jectX\Px\Commands;

use Consolidation\AnnotatedCommand\AnnotatedCommand;
use Consolidation\AnnotatedCommand\AnnotationData;
use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\ProjectX\Plugin\DeployType\DeployTypeInterface;
use Pr0jectX\Px\PxApp;
use Robo\Exception\TaskException;
use Robo\Task\Composer\loadTasks as taskComposer;
use Robo\Task\Filesystem\loadTasks as taskFilesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

/**
 * Define the artifact commands.
 */
class Artifact extends CommandTasksBase
{
    use taskComposer;
    use taskFilesystem;

    /**
     * Build the composer project artifact.
     *
     * @param array $opts
     * @option project-dir The path where the project exists.
     * @option build-dir The path where the artifact will be built.
     */
    public function artifactBuild($opts = [
        'build-dir' => 'build',
        'project-dir' => 'docroot',
        'build-copy' => [],
        'build-mirror' => [],
        'project-copy' => [],
        'project-mirror' => [],
        'remove-submodules' => [],
        'search-submodules-depth' => 1
    ]) {
        $projectRoot = $this->projectRootPath();
        $buildRoot = "{$projectRoot}/{$opts['build-dir']}";

        if (file_exists($buildRoot)) {
            $cleanBuild = $this->confirm(
                'Clean the build directory? [yes]', true
            );
            if ($cleanBuild) {
                $this->taskCleanDir($buildRoot)->run();
            }
        } else {
            $this->_mkdir($buildRoot);
            $this->success(sprintf(
                'Successfully created the "%s" build directory.',
                $buildRoot
            ));
        }

        $this
            ->invokeComposerBuild($buildRoot)
            ->invokeProjectBuild($opts, $projectRoot, $buildRoot);

        $this->invokeSubmoduleCleanup(
            $buildRoot,
            $opts['remove-submodules'],
            $opts['search-submodules-depth']
        );
    }

    /**
     * Deploy the artifact for a given deploy type.
     *
     * @param array $options
     * @option plugin-id Set the deploy type plugin identifier.
     * @option build-path Set the deploy build path.
     *
     * @throws \Robo\Exception\TaskException
     */
    public function artifactDeploy($options = ['plugin-id' => 'git', 'build-dir' => 'build'])
    {
        $buildRoot = PxApp::projectRootPath() . "/{$options['build-dir']}";

        if ($this->isDirEmpty($buildRoot)) {
            $previousCommandName = 'artifact:build';

            $this->io()->warning(sprintf(
                'The %s command needs to run prior to deploying.',
                $previousCommandName
            ));
            $this->runCommand($previousCommandName);
        }
        $plugin_id = $options['plugin-id'];

        $deployTypeInstance = $this->deployTypePluginManager()
            ->createInstance($plugin_id);

        if (!$deployTypeInstance instanceof DeployTypeInterface) {
            throw new TaskException(
                __CLASS__,
                'The deploy type instance is invalid.'
            );
        }
        $deployTypeInstance->deploy();
    }

    /**
     * The artifact deploy option command hook.
     *
     * @hook option artifact:deploy
     *
     * @param \Consolidation\AnnotatedCommand\AnnotatedCommand $command
     *   The annotated command object.
     * @param \Consolidation\AnnotatedCommand\AnnotationData $annotatedData
     *   The annotated data object.
     */
    public function artifactDeployOptions(AnnotatedCommand $command, AnnotationData $annotatedData)
    {
        /** @var DeployTypeInterface $className */
        $className = $this->getClassNameFromInputPluginId();

        if (is_subclass_of($className, DeployTypeInterface::class)) {
            $command->addOptions($className::deployOptions());
        }
    }

    /**
     * Run a symfony command by name.
     *
     * @param $name
     *   The symfony command name.
     * @param array $options
     *   An array of command options.
     * @param bool $default
     *   The confirmation default answer.
     *
     * @return \Pr0jectX\Px\Commands\Artifact
     */
    protected function runCommand($name, $options = [], $default = true)
    {
        $command = $this->findCommand($name);

        if ($command instanceof Command) {
            $runCommand = $this->confirm(
                sprintf('Run the %s command? [%s]', $name, $default ? 'yes' : 'no'),
                $default
            );
            if ($runCommand) {
                $options += $this->getCommandConfigOptions($name);

                $commandTask = $this->taskSymfonyCommand($command);
                foreach ($options as $option => $value) {
                    $commandTask->opt($option, $value);
                }
                $commandTask->run();
            }
        }

        return $this;
    }

    /**
     * Invoke the submodule cleanup.
     *
     * @param $buildRoot
     *   The build root directory.
     * @param array $submoduleDirs
     *   An array of submodule directories.
     * @param int $submoduleSearchDepth
     *   The depth that should be searched for submodules.
     *
     * @return \Pr0jectX\Px\Commands\Artifact
     */
    protected function invokeSubmoduleCleanup(
        $buildRoot,
        array $submoduleDirs = [],
        $submoduleSearchDepth = 1
    )
    {
        $this->removeSubmodulesInVendor($buildRoot);

        foreach ($submoduleDirs as $submoduleDir) {
            $searchDir = "{$buildRoot}/{$submoduleDir}";

            if (!file_exists($searchDir)) {
                continue;
            }
            $this->removeSubmodules(
                [$searchDir], $submoduleSearchDepth
            );
        }

        return $this;
    }

    /**
     * Invoke composer update/install process.
     *
     * @param $buildPath
     *   The fully qualified build path.
     *
     * @return \Pr0jectX\Px\Commands\Artifact
     *   The artifact command class.
     */
    protected function invokeComposerBuild($buildPath)
    {
        $this->moveComposerToBuild($buildPath);

        $updateResult = $this->taskComposerUpdate()
            ->noDev()
            ->preferDist()
            ->workingDir($buildPath)
            ->option('lock')
            ->run();

        if ($updateResult->getExitCode() === 0) {
            $this->success('Composer update has ran successfully.');

            $installResult = $this->taskComposerInstall()
                ->noDev()
                ->preferDist()
                ->option('quiet')
                ->noInteraction()
                ->workingDir($buildPath)
                ->optimizeAutoloader()
                ->run();

            if ($installResult->getExitCode() === 0) {
                $this->success('Composer install has ran successfully.');
            }
        }

        return $this;
    }

    /**
     * Get deploy class name from the input plugin identifier.
     *
     * @return bool|string
     *   The plugin class name based on the plugin identifier.
     */
    protected function getClassNameFromInputPluginId()
    {
        $input = $this->input();

        if (!$input->hasOption('plugin-id')) {
            $choices = $this->deployTypePluginManager()->getOptions();

            if (count($choices) > 1) {
                $pluginId = $this->choice(
                    'What deploy type do you need help with?',
                    $choices
                );
            } else {
                $pluginId = key($choices);
            }
        } else {
            $pluginId = $input->getOption('plugin-id');
        }

        if (!isset($pluginId)) {
            throw new \InvalidArgumentException(
                "Plugin identifier hasn't been defined."
            );
        }

        return $this->deployTypePluginManager()->getClassname($pluginId);
    }

    /**
     * Invoke the project build process.
     *
     * @param $options
     *   An array of the command options.
     * @param $projectRoot
     *   The project root path.
     * @param $buildRoot
     *   The project build root path.
     *
     * @return \Pr0jectX\Px\Commands\Artifact
     *   The artifact command class.
     */
    protected function invokeProjectBuild($options, $projectRoot, $buildRoot)
    {
        $projectDir = $options['project-dir'];

        foreach (['project', 'build'] as $type) {
            $dirs = [$projectDir];

            foreach (['copy', 'mirror'] as $method) {
                $sources = $this->extractSourcesFromOptions(
                    $options, $type, $method
                );
                $this->moveSourceToDestination(
                    $method,
                    $sources,
                    $this->formatPathByType($projectRoot, $type, $dirs),
                    $this->formatPathByType($buildRoot, $type, $dirs)
                );
            }
        }

        return $this;
    }

    /**
     * Get command configuration options.
     *
     * @param $name
     *   The command name.
     *
     * @return array
     *   An array of command configuration options.
     */
    protected function getCommandConfigOptions($name) {
        $configuration = PxApp::getConfiguration();
        $configCommandName = strtr($name, ':', '.');

        return $configuration->get(
            "command.{$configCommandName}.options"
        ) ?: [];
    }
    /**
     * Remove .git submodules in vendor directory.
     *
     * @param $buildRoot
     *   The build root path.
     *
     * @return $this
     */
    protected function removeSubmodulesInVendor($buildRoot)
    {
        $this->removeSubmodules(
            ["{$buildRoot}/vendor"], 2
        );

        return $this;
    }

    /**
     * Remove the .git submodules.
     *
     * @param array $searchDirs
     *   An array of search directories.
     * @param int $depth
     *   Set directory depth for searching.
     *
     * @return $this
     */
    protected function removeSubmodules(array $searchDirs, $depth = 1)
    {
        if (!empty($searchDirs)) {
            $finder = (new Finder())
                ->in($searchDirs)
                ->ignoreVCS(false)
                ->ignoreDotFiles(false)
                ->depth($depth)
                ->name('.git');

            foreach (iterator_to_array($finder) as $filePath => $fileInfo) {
                if (!file_exists($filePath) || !$fileInfo->isDir()) {
                    continue;
                }
                $this->_deleteDir($filePath);
            }
        }

        return $this;
    }

    /**
     * Extract the location from the command options.
     *
     * @param array $options
     *   An array of options passed into the CLI.
     * @param $type
     *   The location level type (e.g. project, build).
     * @param $method
     *   The method on which to execute (e.g. copy, mirror).
     *
     * @return array
     *   An array of locations extracted from the options.
     */
    protected function extractSourcesFromOptions(array $options, $type, $method)
    {
        if (!isset($options["{$type}-{$method}"])
            || empty($options["{$type}-{$method}"])) {
            return [];
        }

        return $options["{$type}-{$method}"];
    }

    /**
     * Format project path by type.
     *
     * @param $path
     *   The root path.
     * @param $type
     *   The the format type.
     * @param array $directories
     *   An array of path directories.
     *
     * @return string
     *   The formatted project path.
     */
    protected function formatPathByType($path, $type, array $directories = [])
    {
        if ($type === 'project') {
            return implode(
                '/', array_merge([$path], $directories)
            );
        }

        return $path;
    }

    /**
     * Move the source to destination.
     *
     * @param $method
     *   The execution method.
     * @param array $sources
     *   The array of sources.
     * @param $fromPath
     *   The source from path.
     * @param $toPath
     *   The destination to path.
     *
     * @return \Pr0jectX\Px\Commands\Artifact
     *   The artifact command class.
     */
    protected function moveSourceToDestination($method, array $sources, $fromPath, $toPath)
    {
        if (empty($sources)) {
            return;
        }
        $stack = $this->taskFilesystemStack();

        foreach ($sources as $source) {
            $fromFilePath = "{$fromPath}/{$source}";

            if (!file_exists($fromFilePath)) {
                continue;
            }
            call_user_func_array(
                [$stack, $method],
                [$fromFilePath, "{$toPath}/$source"]
            );
        }
        $stack->run();

        return $this;
    }

    /**
     * Move composer to the build directory.
     *
     * @param $buildPath
     *   The fully qualified build path.
     *
     * @return \Pr0jectX\Px\Commands\Artifact
     *   The artifact command class.
     */
    protected function moveComposerToBuild($buildPath)
    {
        $stack = $this->taskFilesystemStack();
        $projectRoot = $this->projectRootPath();

        if (file_exists("{$projectRoot}/patches")) {
            $stack->mirror("{$projectRoot}/patches", "{$buildPath}/patches");
        }
        $stack->copy("{$projectRoot}/composer.json", "{$buildPath}/composer.json");
        $stack->copy("{$projectRoot}/composer.lock", "{$buildPath}/composer.lock");

        $result = $stack->run();

        if ($result->getExitCode() === 0) {
            $this->success(
                'Composer files was copied to the build directory.'
            );
        }

        return $this;
    }

    /**
     * Deploy type plugin manager.
     *
     * @return \Pr0jectX\Px\PluginManagerInterface
     */
    protected function deployTypePluginManager()
    {
        return PxApp::getContainer()->get('deployTypePluginManager');
    }
}
