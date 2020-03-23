<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Robo\Tasks;

/**
 * Define the command task base.
 */
abstract class CommandTasksBase extends Tasks
{
    use IOExtraTrait;

    /**
     * Find the application command.
     *
     * @param $name
     *   The name of the command.
     *
     * @return \Symfony\Component\Console\Command\Command
     *   The application command instance.
     */
    protected function findCommand($name)
    {
        return $this->application()->find($name);
    }

    /**
     * Get symfony application service.
     *
     * @return \Pr0jectX\Px\PxApp
     *   Return the project-x application.
     */
    protected function application()
    {
        return PxApp::service('application');
    }

    /**
     * Is the directory empty.
     *
     * @param $directory
     *   The directory to check if it's empty.
     *
     * @return bool
     *   Return true if the directory is empty; otherwise false.
     */
    protected function isDirEmpty($directory): bool
    {
        return !file_exists($directory)
            || !(new \FilesystemIterator($directory))->valid();
    }
}
