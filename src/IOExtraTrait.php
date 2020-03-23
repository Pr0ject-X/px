<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Define the IO extra trait.
 */
trait IOExtraTrait
{
    /**
     * Display the command info message.
     *
     * @param $message
     *   The info message.
     *
     * @return $this
     */
    protected function note(string $message)
    {
        $this->io()->note($message);

        return $this;
    }

    /**
     * Display the command error message.
     *
     * @param $message
     *   The error message.
     *
     * @return $this
     */
    protected function error(string $message)
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
    protected function warning(string $message)
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
    protected function success(string $message)
    {
        $this->io()->success($message);

        return $this;
    }

    /**
     * Display a choice question.
     *
     * @param string $question
     *   The question to ask.
     * @param array $choices
     *   An array of the question choices.
     * @param string $default
     *   The default question answer if empty.
     *
     * @return string
     *   The selected choice response.
     */
    protected function choice(string $question, array $choices, string $default = null): string
    {
        return $this->doAsk(
            new ChoiceQuestion($this->formatQuestionDefault($question, $default), $choices, $default)
        );
    }

    /**
     * Format the question default structure.
     *
     * @param string $question
     *   The question to ask.
     * @param null $default
     *   The question default value.
     *
     * @return string
     *   The formatted question with default value.
     */
    protected function formatQuestionDefault(string $question, $default = null): string
    {
        $formattedQuestion = $question;

        if (is_scalar($default) && isset($default)) {
            $formattedQuestion .= " [{$default}]";
        }

        return $this->formatQuestion("{$formattedQuestion}:");
    }
}
