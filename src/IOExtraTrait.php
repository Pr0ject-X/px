<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

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
     * Ask a question with multiple choices.
     *
     * @param string $question
     *   The question to ask.
     * @param array $choices
     *   An array of the question choices.
     * @param string $default
     *   The default question answer if empty.
     *
     * @return string
     *   The selected choice answer.
     */
    protected function askChoice(string $question, array $choices, string $default = null): string
    {
        return $this->doAsk(
            $this->choice($question, $choices, $default)
        );
    }

    /**
     * Get the formatted choice question.
     *
     * @param string $question
     *   The question to ask.
     * @param array $choices
     *   An array of the question choices.
     * @param string $default
     *   The default question answer if empty.
     *
     * @return \Symfony\Component\Console\Question\ChoiceQuestion
     */
    protected function choice(string $question, array $choices, string $default = null): ChoiceQuestion
    {
        return new ChoiceQuestion(
            $this->formatQuestionDefault($question, $default),
            $choices,
            $default
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

    /**
     * Decorate a question as required.
     *
     * @param \Symfony\Component\Console\Question\Question $question
     *   Input the question object.
     * @param string $message
     *   Input the message to use for the exception.
     * @param callable|null $normalizeCallback
     *   The question normalize callback.
     *
     * @return \Symfony\Component\Console\Question\Question
     */
    protected function requiredQuestion(
        Question $question,
        string $message = 'This field is required!',
        ?callable $normalizeCallback = null
    ): Question {
        $question = ($question)->setValidator(function ($value) use ($message) {
            if (!isset($value)) {
                throw new \RuntimeException(
                    $message
                );
            }
            return $value;
        });

        if (is_callable($normalizeCallback)) {
            $question->setNormalizer($normalizeCallback);
        }

        return $question;
    }
}
