<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ConfigTreeBuilder;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Define configuration tree builder.
 */
class ConfigTreeBuilder
{
    /**
     * @var array
     */
    protected $tree = [];

    /**
     * @var \Symfony\Component\Console\Input\Input
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\Output
     */
    protected $output;

    /**
     * @var bool
     */
    protected $immutable = false;

    /**
     * Set the question input stream.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The symfony input instance.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder
     */
    public function setQuestionInput(InputInterface $input): ConfigTreeBuilder
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Set the question output stream.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The symfony input instance.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder
     */
    public function setQuestionOutput(OutputInterface $output): ConfigTreeBuilder
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Set the configure tree as immutable.
     *
     * @return ConfigTreeBuilder
     */
    public function isImmutable(): ConfigTreeBuilder
    {
        $this->immutable = true;

        return $this;
    }

    /**
     * Check if the tree is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->tree);
    }

    /**
     * Set the configuration tree node.
     *
     * @param string $name
     *   The node name.
     *
     * @return ConfigNode
     *
     * @throws \Exception
     */
    public function createNode(string $name): ConfigNode
    {
        if (!isset($this->tree[$name])) {
            $this->tree[$name] = new ConfigNode($this);
        } elseif ($this->immutable) {
            throw new \Exception(
                'Unable to override as the config tree is immutable.'
            );
        }

        return $this->tree[$name];
    }

    /**
     * Build the configuration tree.
     *
     * @return array
     */
    public function build(): array
    {
        $build = [];

        /** @var \Pr0jectX\Px\ConfigNode $node */
        foreach ($this->tree as $name => $node) {
            if (!$this->hasConditionPassed($node, [$build])) {
                continue;
            }
            $prevBuild = $build;
            $build[$name] = [];

            $this->processNodeValues($node->getValue(), $name, $build[$name], $prevBuild);

            $build[$name] = $this->formatNodeBuildValue($node, $build[$name]);
        }

        return $build;
    }

    /**
     * Process node values that were defined.
     *
     * @param array $values
     *   An array of values to iterate over.
     * @param string $name
     *   A node property name.
     * @param array $data
     *   An array of the current node values.
     * @param array $previousData
     *   An array of previous node values data.
     */
    protected function processNodeValues(array $values, string $name, array &$data = [], array $previousData = []): void
    {
        foreach ($values as $index => $value) {
            if ($value instanceof ConfigNodeArray) {
                if (!isset($data[$index])) {
                    $data[$index] = [];
                }
                $this->processNodeValues($value->getValue(), $name, $data[$index]);
            } else {
                if (is_scalar($value)) {
                    $data[$index] = $value;
                }
                if ($value instanceof Question) {
                    $data[$index] = (new QuestionHelper())->ask(
                        $this->input,
                        $this->output,
                        $value
                    );
                }
                if (is_callable($value)) {
                    $data[$index] = $value(
                        array_merge_recursive($previousData, [$name => $data])
                    );
                }
            }
        }
    }

    /**
     * Format the node build value output.
     *
     * @param \Pr0jectX\Px\ConfigTreeBuilder\ConfigNode $node
     *   The configuration node.
     * @param array $value
     *   The node array value.
     *
     * @return mixed
     *   The formatted build values.
     */
    protected function formatNodeBuildValue(ConfigNode $node, array $value)
    {
        if (!$node->hasMultipleValues() && !$node->hasNodeArrayValue()) {
            return reset($value);
        }

        if ($node->hasNodeArrayValue() && $node->countNodeArrayValue() === 1) {
            $nodeArray = $node->getValue()[0];

            if ($nodeArray instanceof ConfigNodeArray && $nodeArray->hasSingleValue()) {
                return reset($value);
            }
        }

        return $value;
    }

    /**
     * Has config node condition passed.
     *
     * @param \Pr0jectX\Px\ConfigTreeBuilder\ConfigNode $node
     *   A configuration node object.
     * @param array $args
     *   An array of arguments to pass to the condition
     * @param bool $mustPassAll
     *   Determine if all condition must pass the requirements.
     *
     * @return bool
     *   Return true if the node condition has passed; otherwise false.
     */
    protected function hasConditionPassed(
        ConfigNode $node,
        array $args = [],
        bool $mustPassAll = false
    ): bool {
        if (!$node->hasCondition()) {
            return true;
        }
        $verdict = [];

        foreach ($node->getCondition() as $condition) {
            $verdict[] = call_user_func_array($condition, $args);
        }

        if ($verdict = array_unique($verdict)) {
            if (count($verdict) === 1) {
                return reset($verdict);
            }

            if ($mustPassAll !== true) {
                return true;
            }
        }

        return false;
    }
}
