<?php

namespace Droath\ProjectX;

use Robo\Tasks;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Define the command task base.
 */
abstract class CommandTasksBase extends Tasks
{
    /**
     * Is the directory empty.
     *
     * @param $directory
     *   The directory to check if it's empty.
     *
     * @return bool
     *   Return true if the directory is empty; otherwise false.
     */
    protected function isDirEmpty($directory)
    {
        return !file_exists($directory)
            || !(new \FilesystemIterator($directory))->valid();
    }

    /**
     * Display a choice question.
     *
     * @param $question
     *   The question to ask.
     * @param array $choices
     *   An array of the question choices.
     * @param null $default
     *   The default question answer if empty.
     *
     * @return string
     *   The selected choice response.
     */
    protected function choice($question, array $choices, $default = null) {
        return $this->doAsk(
            new ChoiceQuestion($question, $choices, $default)
        );
    }

    /**
     * Display the command error message.
     *
     * @param $message
     *   The error message.
     *
     * @return $this
     */
    protected function error($message)
    {
        $this->io()->error($message);

        return $this;
    }

    /**
     * Display the command warning message.
     *
     * @param $message
     *   The warning message.
     *
     * @return $this
     */
    protected function warning($message)
    {
        $this->io()->warning($message);

        return $this;
    }

    /**
     * Display the command success message.
     *
     * @param $message
     *   The success message.
     *
     * @return $this
     */
    protected function success($message)
    {
        $this->io()->success($message);

        return $this;
    }

    /**
     * Get the project root path.
     *
     * @return bool|string
     *   The project root path; otherwise false if not found.
     */
    protected function projectRootPath()
    {
        return PxApp::projectRootPath();
    }

    /**
     * Get the command input.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    protected function input()
    {
        return PxApp::getContainer()->get('input');
    }

    /**
     * Get the command output.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function output()
    {
        return PxApp::getContainer()->get('output');
    }

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
     * @return \Droath\ProjectX\PxApp
     *   Return the project-x application.
     */
    protected function application()
    {
        return PxApp::service('application');
    }
}
