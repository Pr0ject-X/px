<?php

namespace Pr0jectX\Px\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Packagist\Api\Client;
use Packagist\Api\Result\Result;
use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\CommonCommandTrait;
use Pr0jectX\Px\HookExecuteType\ExecuteHookTaskTrait;
use Pr0jectX\Px\PxApp;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Define the project-x core command.
 */
class Core extends CommandTasksBase
{
    use CommonCommandTrait;
    use ExecuteHookTaskTrait;

    /**
     * @var string
     */
    const DEFAULT_PROJECT_FILE = 'projects.json';

    /**
     * @hook pre-command *
     *
     * @param \Consolidation\AnnotatedCommand\CommandData $commandData
     */
    public function corePreCommandHook(CommandData $commandData)
    {
        $this->executeHookCollectionTasks($commandData, 'pre');
    }

    /**
     * @hook post-command *
     *
     * @param $result
     *   The post command result.
     *
     * @param \Consolidation\AnnotatedCommand\CommandData $commandData
     */
    public function corePostCommandHook($result, CommandData $commandData)
    {
        $this->executeHookCollectionTasks($commandData, 'post');
    }

    /**
     * Search and install project-x plugins.
     *
     * @param string $searchTerm
     *   The search term to filter out plugins; otherwise all plugins are shown.
     *
     * @param array $opts
     * @option $working-directory The composer working directory.
     *
     * @aliases install
     */
    public function coreInstall(string $searchTerm = null, $opts = ['working-directory' => null]): void
    {
        $query = $searchTerm ?? "";
        $options = $this->getProjectXPluginOptions($query);

        if (empty($options)) {
            $this->error(
                sprintf("Unable to locate a project-x plugin matching '%s'!
                \r\t Please try again with another search term.", $query)
            );
            return;
        }

        $packageNames = $this->doAsk(
            (new ChoiceQuestion(
                $this->formatQuestion('Select the project-x plugin(s) to install?'),
                $options
            ))->setMultiselect(true)
        );

        $packageList = implode(',', $packageNames);
        $interaction = !$opts['no-interaction'] ?? true;
        $workingDirectory = $opts['working-directory']
            ?? PxApp::projectRootPath();

        $result = $this->taskComposerRequire()
            ->dev()
            ->args($packageNames)
            ->interactive($interaction)
            ->workingDir($workingDirectory)
            ->run();

        if (!$result->wasSuccessful()) {
            $this->error(
                sprintf('The %s plugin had an error on installation.', $packageList)
            );
            return;
        }

        $this->success(
            sprintf('The %s plugin was successfully installed.', $packageList)
        );
    }

    /**
     * Save the project location.
     *
     * @param string|null $name
     *   The name of the project.
     * @param array $opts
     * @option $edit
     *   Set if you want to manually edit this file.
     */
    public function coreSave(string $name = null, array $opts = ['edit' => false]): void
    {
        if (!isset($opts['edit']) || !$opts['edit']) {
            $name = isset($name) ? $name : $this->askProjectName();
            $path = PxApp::projectRootPath();

            $project[$path] = [
                'name' => $name,
                'path' => $path
            ];
            $data = array_replace(
                $this->loadGlobalProjectData(),
                $project
            );

            if ($status = $this->saveGlobalProject($data)) {
                $this->success(
                    sprintf('The "%s" project was successfully added!', $name)
                );
                return;
            }
            $this->error('The "%s" project failed to be added!', $name);
        }

        $this->taskExec("open {$this->globalProjectFilename()}")->run();
    }

    /**
     * Remove the project location.
     */
    public function coreRemove()
    {
        if ($this->globalProjectExist()) {
            $data = $this->loadGlobalProjectData();
            $name = $data[PxApp::projectRootPath()]['name'];
            unset($data[PxApp::projectRootPath()]);

            if ($status = $this->saveGlobalProject($data)) {
                $this->success(
                    sprintf('The "%s" project was successfully removed!', $name)
                );
            }
            return;
        }
        $this->note("The project doesn't exist yet! Use core:save to add the project.");
    }

    /**
     * Switch to another saved project directory.
     *
     * @param array $opts
     * @option $raw
     *   If set the raw output will be returned.
     * @aliases switch
     */
    public function coreSwitch($opts = ['raw' => false]): void
    {
        $options = $this->globalProjectOptions();

        $project = $this->askChoice(
            'Select the project',
            $options
        );
        if (!isset($options[$project])) {
            throw new \RuntimeException(
                sprintf('The project "%s" is an invalid option.', $project)
            );
        }
        $changeDir = $options[$project];

        if ($changeDir !== PxApp::projectRootPath()) {
            if (isset($opts['raw']) && $opts['raw']) {
                print "$changeDir";
                return;
            }
            $command = "cd {$changeDir} && vendor/bin/px env:up";

            system("echo \"{$command}\"|pbcopy");

            $this->success(sprintf(
                "Command '%s' was copied to clipboard. Use CMD+V to paste into the terminal.",
                $command
            ));

            if ($this->confirm('Stop the current project environment?', true)) {
                if ($command = $this->findCommand('env:stop')) {
                    $this->taskSymfonyCommand($command)->run();
                }
            }
            return;
        }

        if (isset($opts['raw']) && !$opts['raw']) {
            $this->note(
                "You're already working on that project! Take a break and get some coffee!"
            );
        }
    }

    /**
     * Add the CLI integration into your shell (e.g .bashrc, .zshrc).
     */
    public function coreCliShortcut(): void
    {
        $userShellRcFile = $this->getUserShellRcFile();

        if (!file_exists($userShellRcFile)) {
            throw new \Exception(
                sprintf('The %s file does not exist.', $userShellRcFile)
            );
        }

        $continue = $this->confirm(
            sprintf('Add the CLI integration to %s?', $userShellRcFile),
            true
        );

        if ($continue) {
            $shortcutResult = $this->taskWriteToFile($userShellRcFile)
                ->append()
                ->appendUnlessMatches('/function px\(\)\n{/', $this->getShortcutContents())
                ->run();

            $switchResult = $this->taskWriteToFile($userShellRcFile)
                ->append()
                ->appendUnlessMatches('/function px-switch\(\)\n{/', $this->getSwitcherContents())
                ->run();

            if ($shortcutResult->wasSuccessful() || $switchResult->wasSuccessful()) {
                $this->success(sprintf(
                    'Successfully added the CLI integration to %s.',
                    $userShellRcFile
                ));

                $this->note(sprintf(
                    'Run `source %s` to finalize the process.',
                    $userShellRcFile
                ));
            }
        }
    }

    /**
     * Get host user shell RC filepath.
     *
     * @return string
     *   The host environment user shell RC filepath.
     */
    protected function getUserShellRcFile(): string
    {
        $userShell = PxApp::userShell();
        $userHomeDir = PxApp::userDir();

        return "{$userHomeDir}/.{$userShell}rc";
    }

    /**
     * Get the CLI shortcut function contents.
     *
     * @return string
     *   The CLI shortcut function contents.
     */
    protected function getShortcutContents(): string
    {
        return file_get_contents(APPLICATION_ROOT . '/templates/core/shortcut.txt');
    }

    /**
     * Get the CLI switcher function contents.
     *
     * @return string
     *   The CLI switch function contents.
     */
    protected function getSwitcherContents(): string
    {
        return file_get_contents(APPLICATION_ROOT . '/templates/core/switcher.txt');
    }

    /**
     * Get the project-x plugin options.
     *
     * @param string $query
     *   The project-x plugin search query.
     *
     * @return array
     *   An array of project-x plugin options.
     */
    protected function getProjectXPluginOptions(string $query): array
    {
        $options = [];

        /** @var \Packagist\Api\Result\Result $result */
        foreach ($this->searchProjectXPlugins($query) as $result) {
            if (!$result instanceof Result) {
                continue;
            }
            $options[] = $result->getName();
        }

        return $options;
    }

    /**
     * Search for a project-x plugin based on the given query.
     *
     * @param string $query
     *   The project-x plugin search query.
     *
     * @return array
     *   An array of \Packagist\Api\Result\Result objects.
     */
    protected function searchProjectXPlugins(string $query): array
    {
        return (new Client())->search(
            $query,
            ['type' => 'px-plugin']
        );
    }

    /**
     * Ask to input the project name.
     *
     * @return string
     *   The name of the project.
     */
    protected function askProjectName()
    {
        return $this->doAsk(
            (new Question(
                $this->formatQuestionDefault('Input project name')
            ))->setValidator(function ($value) {
                if (!isset($value) || empty($value)) {
                    throw new \RuntimeException(
                        'Project name is required!'
                    );
                }

                return $value;
            })
        );
    }

    /**
     * Get global project options.
     *
     * @return array
     *   An array of project options.
     */
    protected function globalProjectOptions(): array
    {
        $options = [];

        foreach ($this->loadGlobalProjectData() as $path => $data) {
            if (!isset($data['name'])) {
                continue;
            }
            $options[strtr(strtolower($data['name']), ' ', '-')] = $path;
        }

        return $options;
    }

    /**
     * Save the global project contents.
     *
     * @param array $data
     *   An array of the project data.
     *
     * @return bool
     *   Return true if successfully; otherwise false.
     */
    protected function saveGlobalProject(array $data): bool
    {
        $results = $this
            ->taskWriteToFile($this->globalProjectFilename())
            ->text(json_encode($data, JSON_PRETTY_PRINT))
            ->run();

        return $results->wasSuccessful();
    }

    /**
     * Get the global project filename.
     *
     * @return string
     *   The global project filename.
     */
    protected function globalProjectFilename(): string
    {
        $globalTempDir = PxApp::globalTempDir();
        $projectFile = static::DEFAULT_PROJECT_FILE;

        return "{$globalTempDir}/{$projectFile}";
    }

    /**
     * Determine if the project exist.
     *
     * @return bool
     *   Return true if the project; otherwise false.
     */
    protected function globalProjectExist(): bool
    {
        return isset($this->loadGlobalProjectData()[PxApp::projectRootPath()]);
    }

    /**
     * Load global project data.
     *
     * @return array
     *   An array of the global projects.
     */
    protected function loadGlobalProjectData(): array
    {
        $projects = [];
        $projectFilename = $this->globalProjectFilename();

        if (file_exists($projectFilename)) {
            if ($contents = file_get_contents($projectFilename)) {
                $projects = json_decode($contents, true);
            }
        }

        return $projects;
    }
}
