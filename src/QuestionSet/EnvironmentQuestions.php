<?php

namespace Pr0jectX\Px\QuestionSet;

use Pr0jectX\Px\PluginManagerInterface;
use Pr0jectX\Px\PxApp;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Define the environment question sets.
 */
class EnvironmentQuestions implements QuestionSetInterface
{
    /**
     * {@inheritDoc}
     */
    public function questions()
    {
        $collection = new QuestionCollection();

        if ($envTypeOptions = $this->getEnvTypeOptions()) {
            $collection->addQuestion(
                'type',
                new ChoiceQuestion('Environment Type: ', $envTypeOptions)
            );
        }

        return $collection;
    }

    /**
     * Get the environment type options.
     *
     * @return array
     *   An array of environment options.
     */
    protected function getEnvTypeOptions()
    {
        /** @var PluginManagerInterface $envTypePluginManager */
        $envTypePluginManager = PxApp::getContainer()
            ->get('environmentTypePluginManager');

        return $envTypePluginManager->getOptions();
    }
}
