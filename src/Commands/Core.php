<?php

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\CommonCommandTrait;
use Pr0jectX\Px\PxApp;
use Symfony\Component\Console\Question\Question;

/**
 * Define the project-x core command.
 */
class Core extends CommandTasksBase
{
    use CommonCommandTrait;

    /**
     * @var string
     */
    const DEFAULT_PROJECT_FILE = 'projects.json';

    /**
     * Save the project location.
     *
     * @param string|null $name
     *   The name of the project.
     * @param array $opts
     * @option $edit
     *   Set if you want to manually edit this file.
     */
    public function coreSave(string $name = null, array $opts = ['edit' => false])
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
    public function coreSwitch($opts = ['raw' => false])
    {
        $options = $this->globalProjectOptions();

        $project = $this->choice(
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
                exit();
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
    public function coreCliShortcut()
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
     * Get host user home.
     *
     * @return string|null
     *   The host environment user home.
     */
    protected function getUserHome(): string
    {
        return getenv('HOME') ?: null;
    }

    /**
     * Get host user shell.
     *
     * @return string|null
     *   The host environment user shell.
     */
    protected function getUserShell(): string
    {
        $shell = getenv('SHELL');

        return substr(
            $shell,
            strrpos($shell, '/') + 1
        ) ?: '';
    }

    /**
     * Get host user shell RC filepath.
     *
     * @return string
     *   The host environment user shell RC filepath.
     */
    protected function getUserShellRcFile(): string
    {
        return "{$this->getUserHome()}/.{$this->getUserShell()}rc";
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
