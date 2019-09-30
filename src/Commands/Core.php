<?php

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\CommonCommandTrait;

/**
 * Define the project-x core command.
 */
class Core extends CommandTasksBase
{
    use CommonCommandTrait;

    /**
     * Add the CLI project-x function to your shell (e.g .bashrc, .zshrc).
     */
    public function coreCliShortcut ()
    {
        $userShellRcFile = $this->getUserShellRcFile();

        if (!file_exists($userShellRcFile)) {
            throw new \Exception(
                sprintf('The %s file does not exist.', $userShellRcFile)
            );
        }

        $addShortcut = $this->confirm(
            sprintf('Add the project-x function to %s?', $userShellRcFile),
            true
        );

        if ($addShortcut) {
            $templateFile = APPLICATION_ROOT . '/templates/core/shortcut.txt';
            $templateContents = file_get_contents($templateFile);

            $this->taskWriteToFile($userShellRcFile)
                ->append()
                ->appendUnlessMatches('/function px\(\)\n{/', $templateContents)
                ->run();

            $this->success(sprintf(
                'Successfully added the project-x function to %s.',
                $userShellRcFile
            ))->info(sprintf(
                'Run `source %s` to finalize the process.',
                $userShellRcFile
            ));
        }
    }

    /**
     * Get host user home.
     *
     * @return string|null
     *   The host environment user home.
     */
    protected function getUserHome()
    {
        return getenv('HOME') ?: NULL;
    }

    /**
     * Get host user shell.
     *
     * @return string|null
     *   The host environment user shell.
     */
    protected function getUserShell()
    {
        $shell = getenv('SHELL');

        return substr(
            $shell, strrpos($shell, '/') + 1
        ) ?: NULL;
    }

    /**
     * Get host user shell RC filepath.
     *
     * @return string
     *   The host environment user shell RC filepath.
     */
    protected function getUserShellRcFile()
    {
        return "{$this->getUserHome()}/.{$this->getUserShell()}rc";
    }
}
