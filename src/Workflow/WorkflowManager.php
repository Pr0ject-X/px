<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Workflow;

use Consolidation\Config\ConfigInterface;
use Pr0jectX\Px\Contracts\WorkflowPluginInterface;
use Robo\ClassDiscovery\RelativeNamespaceDiscovery;
use Robo\Collection\CollectionBuilder;
use Robo\Result;

/**
 * Define the workflow manager.
 */
class WorkflowManager
{
    /**
     * @var \Consolidation\Config\ConfigInterface
     */
    protected $config;

    /**
     * @var array
     */
    protected $workflows;

    /**
     * @var \Robo\ClassDiscovery\RelativeNamespaceDiscovery
     */
    protected $classDiscovery;

    /**
     * Constructor for the workflow manager.
     *
     * @param \Consolidation\Config\ConfigInterface $config
     *   The configuration service.
     * @param \Robo\ClassDiscovery\RelativeNamespaceDiscovery $classDiscovery
     *   The class discovery service.
     */
    public function __construct(
        ConfigInterface $config,
        RelativeNamespaceDiscovery $classDiscovery
    ) {
        $this->config = $config;
        $this->classDiscovery = $classDiscovery;
    }

    /**
     * Get all workflow instances.
     *
     * @return \Pr0jectX\Px\Workflow\Workflow[]
     *   An array of workflow instances.
     */
    public function getWorkflows(): array
    {
        if (!isset($this->workflow)) {
            $this->workflows = $this->discoverWorkflows();
        }

        return $this->workflows;
    }

    /**
     * Get the workflow instance.
     *
     * @param string $name
     *   The workflow instance name.
     *
     * @return \Pr0jectX\Px\Workflow\Workflow
     *   The workflow instance.
     */
    public function getWorkflow(string $name): Workflow
    {
        $workflows = $this->getWorkflows();

        if (!isset($workflows[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Workflow %s is invalid!', $name)
            );
        }

        return $workflows[$name];
    }

    /**
     * Get the workflow options.
     *
     * @return array
     *   An array of workflow options.
     */
    public function getWorkflowOptions(): array
    {
        $options = [];

        foreach ($this->getWorkflows() as $name => $workflow) {
            $options[$name] = $workflow->getLabel();
        }

        return $options;
    }

    /**
     * Run the workflow job commands.
     *
     * @param string $name
     *   The workflow name.
     * @param \Robo\Collection\CollectionBuilder $collection
     *   The robo collection builder.
     *
     * @return array
     *   An array of job names keyed by status type (success, error).
     */
    public function runWorkflow(
        string $name,
        CollectionBuilder $collection
    ): array {
        $status = [];
        $workflow = $this->getWorkflow($name);

        /** @var \Robo\Result $result */
        foreach ($workflow->run($collection) as $jobName => $result) {
            $statusType = $result->getExitCode() === Result::EXITCODE_OK
                ? 'success'
                : 'error';

            $status[$statusType][] = $jobName;
        }

        return $status;
    }

    /**
     * Load the workflows from the configuration.
     *
     * @return array
     *   An array of discovered workflows.
     */
    protected function discoverWorkflows(): array
    {
        return array_merge(
            $this->findWorkflowInstances(),
            $this->findWorkflowPluginInstances()
        );
    }

    /**
     * Locate the workflow instances.
     *
     * @return \Pr0jectX\Px\Workflow\Workflow[]
     */
    protected function findWorkflowInstances(): array
    {
        $instances = [];

        if ($workflows = $this->config->get('workflow', [])) {
            foreach ($workflows as $name => $definitions) {
                $instances[$name] = new Workflow($definitions);
            }
        }

        return $instances;
    }

    /**
     * Locate the workflow plugin instances.
     *
     * @return \Pr0jectX\Px\Workflow\Workflow[]
     *   An array of the workflow plugin instances.
     */
    protected function findWorkflowPluginInstances(): array
    {
        $workflows = [];

        foreach ($this->getWorkflowPlugins() as $workflowPluginClassName) {
            if (
                !class_exists($workflowPluginClassName)
                || !is_subclass_of($workflowPluginClassName, WorkflowPluginInterface::class)
            ) {
                continue;
            }
            $workflowPlugin = new $workflowPluginClassName();

            $workflowName = strtolower(
                str_replace(' ', '-', $workflowPlugin->name())
            );

            $workflows[$workflowName] = $workflowPlugin->instance();
        }

        return $workflows;
    }

    /**
     * Get third-party workflow plugin classes.
     *
     * @return array
     *   An array of third-party workflow plugins.
     */
    protected function getWorkflowPlugins(): array
    {
        return $this->classDiscovery
            ->setRelativeNamespace('ProjectX\Plugin\Workflow')
            ->setSearchPattern('/.*Workflow\.php$/')
            ->getClasses();
    }
}
