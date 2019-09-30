<?php

namespace Pr0jectX\Px\QuestionSet;

use Symfony\Component\Console\Question\Question;

/**
 * Define the question collection.
 */
class QuestionCollection implements \IteratorAggregate
{
    protected $questions = [];

    /**
     * Add question to the collection.
     *
     * @param $name
     *   The question unique name.
     * @param Question $question
     *   The question instance.
     */
    public function addQuestion($name, Question $question) {
        $this->questions[$name] = $question;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->questions);
    }
}
