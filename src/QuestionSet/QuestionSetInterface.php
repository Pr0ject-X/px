<?php

namespace Pr0jectX\Px\QuestionSet;

/**
 * Define the question set interface.
 */
interface QuestionSetInterface
{
    /**
     * Define the questions that should be asked.
     *
     * @return QuestionCollection
     */
    public function questions();
}
