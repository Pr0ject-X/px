<?php

namespace Pr0jectX\Px\Commands;

use Pr0jectX\Px\CommandTasksBase;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\QuestionSet\EnvironmentQuestions;
use Pr0jectX\Px\QuestionSet\QuestionCollection;

/**
 * Define the configuration command.
 */
class Config extends CommandTasksBase
{
    /**
     * Set the project configuration.
     *
     * @param $name
     *   The configuration name.
     *
     * @throws \Exception
     */
    public function configSet($name = null)
    {
        if (!isset($name)) {
            $name = $this->choice(
                'Configuration to set?', $this->configOptions()
            );
        }
        $config = $this->buildConfiguration($name);

       if (!empty($config)) {
           PxApp::getConfiguration()
               ->set($name, $config)
               ->save();
       }
    }

    /**
     * Define the configuration router.
     *
     * @return array
     *   An array of configurations.
     */
    protected function configRouter()
    {
        return [
            'environment' => [
                'class' => EnvironmentQuestions::class
            ]
        ];
    }

    /**
     * An array of configuration options.
     *
     * @return array
     *   An array of configuration options.
     */
    protected function configOptions()
    {
        $options = [];

        foreach (array_keys($this->configRouter()) as $name) {
            $options[] = $name;
        }

        return $options;
    }

    /**
     * Build configuration array.
     *
     * @param $name
     *   The configuration name.
     *
     * @return array
     *   An array of the build configuration.
     *
     * @throws \Exception
     */
    protected function buildConfiguration($name)
    {
        $config = [];

        foreach ($this->loadQuestions($name) as $type => $question) {
            $config[$type] = $this->doAsk($question);
        }

        return $config;
    }

    /**
     * Load the config router questions.
     *
     * @param $name
     *   The configuration name.
     *
     * @return QuestionCollection
     *   The question collection instance.
     *
     * @throws \Exception
     */
    protected function loadQuestions($name)
    {
        $router = $this->configRouter();

        if (!isset($router[$name])) {
            throw new \Exception(
                sprintf('The %s is not a valid config name.', $name)
            );
        }
        $classname = $router[$name]['class'];

        if (!class_exists($classname)) {
            throw new \Exception(
                sprintf('The %s class name does not exist.', $classname)
            );
        }

        return (new $classname)->questions();
    }
}
