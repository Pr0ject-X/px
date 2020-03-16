<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ConfigTreeBuilder;

/**
 * Define the configuration node.
 */
class ConfigNode
{
    /**
     * @var ConfigTreeBuilder
     */
    protected $tree;

    /**
     * @var array
     */
    protected $nodeValue;

    /**
     * @var array
     */
    protected $nodeConditions = [];

    /**
     * @var bool
     */
    protected $nodeValueHasArray = false;

    /**
     * Define the ConfigNode constructor.
     *
     * @param ConfigTreeBuilder $tree
     */
    public function __construct(ConfigTreeBuilder $tree)
    {
        $this->tree = $tree;
    }

    /**
     * Set the configuration node value.
     *
     * @param $value
     *   The configuration node value; support Question and literals.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigNode
     */
    public function setValue($value) : ConfigNode
    {
        $this->nodeValue[] = $value;

        return $this;
    }

    /**
     * Set the configuration node array.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigNodeArray
     */
    public function setArray() : ConfigNodeArray
    {
        $array = new ConfigNodeArray($this);

        $this->nodeValue[] = $array;

        $this->nodeValueHasArray = true;

        return $array;
    }

    /**
     * Get configuration node conditions.
     *
     * @return array
     */
    public function getCondition() : array
    {
        return $this->nodeConditions;
    }

    /**
     * Check if the configuration node has conditions.
     *
     * @return bool
     */
    public function hasCondition() : bool
    {
        return isset($this->nodeConditions) && !empty($this->nodeConditions);
    }

    /**
     * Set the configuration node condition.
     *
     * @param callable $condition
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigNode
     */
    public function setCondition(callable $condition) : ConfigNode
    {
        $this->nodeConditions[] = $condition;

        return $this;
    }

    /**
     * Get the tree node value.
     *
     * @return array
     *   An array of node values.
     */
    public function getValue() : array
    {
        return $this->nodeValue;
    }

    /**
     * The tree node has an array value.
     *
     * @return bool
     */
    public function hasNodeArrayValue() : bool
    {
        return $this->nodeValueHasArray;
    }

    /**
     * The tree node has multiple value.
     *
     * @return bool
     *   Return true if has multiple values; otherwise false.
     */
    public function hasMultipleValues() : bool
    {
        return count($this->nodeValue) > 1;
    }

    /**
     * End the tree node statement.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigTreeBuilder
     */
    public function end() : ConfigTreeBuilder
    {
        return $this->tree;
    }
}
