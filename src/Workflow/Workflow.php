<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Workflow;

use Pr0jectX\Px\ExecuteType\ExecuteTypeManager;
use Pr0jectX\Px\PxApp;
use Robo\Collection\CollectionBuilder;

/**
 * Define the project-X workflow class.
 */
class Workflow
{
    /**
     * @var array
     */
    protected $jobs;

    /**
     * @var array
     */
    protected $definition;

    /**
     * Define the workflow constructor.
     *
     * @param array $definition
     *   The workflow definition array.
     */
    public function __construct(
        array $definition
    ) {
        $this->definition = $definition;
    }

    /**
     * Get the workflow label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->definition['label'] ?? '';
    }

    /**
     * Run the workflow commands.
     *
     * @param \Robo\Collection\CollectionBuilder $collection
     *   The collection builder instance.
     *
     * @return array
     *   Return an array of status results for each job executed.
     */
    public function run(CollectionBuilder $collection): array
    {
        $status = [];

        foreach ($this->getWorkflowJobs() as $job) {
            $status[$job->getName()] = $this->executeTypeManager()
                ->executeInstances(
                    $job->getCommandExecuteTypes(),
                    $collection
                );
        }

        return $status;
    }

    /**
     * Get the Workflow attached jobs.
     *
     * @return array|\Pr0jectX\Px\Workflow\WorkflowJob[]
     *   An array of workflow job instances.
     */
    protected function getWorkflowJobs(): array
    {
        if (!isset($this->jobs) || empty($this->jobs)) {
            $this->jobs = $this->loadWorkflowJobs();
        }

        return $this->jobs;
    }

    /**
     * Load the Workflow job instances.
     *
     * @return array|\Pr0jectX\Px\Workflow\WorkflowJob[]
     *   An array of workflow job instances.
     */
    protected function loadWorkflowJobs(): array
    {
        $jobs = [];

        foreach ($this->workflowJobDefinitions() as $name => $definition) {
            if (is_array($definition)) {
                $definition = new WorkflowJob($name, $definition);
            }

            $jobs[$name] = $definition;
        }

        return $jobs;
    }

    /**
     * Return the Workflow job definitions.
     *
     * @return array
     *   An array of the workflow job definition.
     */
    protected function workflowJobDefinitions(): array
    {
        return $this->definition['jobs'] ?? [];
    }

    /**
     * Get execute type manager.
     *
     * @return \Pr0jectX\Px\ExecuteType\ExecuteTypeManager
     */
    protected function executeTypeManager(): ExecuteTypeManager
    {
        return PxApp::service('executeTypeManager');
    }
}
