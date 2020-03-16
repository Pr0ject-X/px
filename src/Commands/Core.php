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
     * Add the CLI integration into your shell (e.g .bashrc, .zshrc).
     */
    public function coreCliShortcut ()
    {
        $userShellRcFile = $this->getUserShellRcFile();

        if (!file_exists($userShellRcFile)) {
            throw new \Exception(
                sprintf('The %s file does not exist.', $userShellRcFile)
            );
        }

        $continue = $this->confirm(
            sprintf('Add the CLI integration to %s?', $userShellRcFile), true
        );

        if ($continue) {
            $result = $this->taskWriteToFile($userShellRcFile)
                ->append()
                ->appendUnlessMatches('/function px\(\)\n{/', $this->getShortcutContents())
                ->run();

            if ($result->wasSuccessful()) {
                $this->success(sprintf(
                    'Successfully added the px function to %s.', $userShellRcFile
                ));

                $this->note(sprintf(
                    'Run `source %s` to finalize the process.', $userShellRcFile
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
    protected function getUserHome() : string
    {
        return getenv('HOME') ?: NULL;
    }

    /**
     * Get host user shell.
     *
     * @return string|null
     *   The host environment user shell.
     */
    protected function getUserShell() : string
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
    protected function getUserShellRcFile() : string
    {
        return "{$this->getUserHome()}/.{$this->getUserShell()}rc";
    }

    /**
     * Get the shortcut contents.
     *
     * @return string
     *   The CLI shortcut contents.
     */
    protected function getShortcutContents() : string
    {
        return file_get_contents(APPLICATION_ROOT . '/templates/core/shortcut.txt');
    }
}
