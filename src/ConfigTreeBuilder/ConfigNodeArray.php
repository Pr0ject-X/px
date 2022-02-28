<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ConfigTreeBuilder;

/**
 * The configuration node array object.
 */
class ConfigNodeArray
{
    /**
     * @var array
     */
    protected $arrayValue;

    /**
     * @var \Pr0jectX\Px\ConfigNode
     */
    protected $configNode;

    /**
     * @var bool
     */
    protected $singleValue = false;

    /**
     * Config node array constructor.
     *
     * @param \Pr0jectX\Px\ConfigTreeBuilder\ConfigNode $node
     *   The config node object.
     * @param bool $singleValue
     *   Set the node a single array value.
     */
    public function __construct(ConfigNode $node, bool $singleValue = false)
    {
        $this->configNode = $node;
        $this->singleValue = $singleValue;
    }

    /**
     * Determine if the value was set as a single.
     *
     * @return bool
     *   Return true if it's a single value; otherwise false.
     */
    public function hasSingleValue(): bool
    {
        return $this->singleValue;
    }

    /**
     * Set array value using a dynamic index.
     *
     * @param scalar|\Symfony\Component\Console\Question\Question $value
     *   The array value.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigNodeArray
     */
    public function setIndexValue($value): ConfigNodeArray
    {
        $this->arrayValue[] = $value;

        return $this;
    }

    /**
     * Set array value using a index key.
     *
     * @param string $key
     *   The array key index.
     * @param scalar|\Symfony\Component\Console\Question\Question $value
     *   The array value.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigNodeArray
     */
    public function setKeyValue(string $key, $value): ConfigNodeArray
    {
        $this->arrayValue[$key] = $value;

        return $this;
    }

    /**
     * Get the node array value.
     *
     * @return array
     */
    public function getValue(): array
    {
        return $this->arrayValue;
    }

    /**
     * The node array end statement.
     *
     * @return \Pr0jectX\Px\ConfigTreeBuilder\ConfigNode
     */
    public function end(): ConfigNode
    {
        return $this->configNode;
    }
}
