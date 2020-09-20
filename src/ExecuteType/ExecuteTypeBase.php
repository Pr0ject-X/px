<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecuteType;

use Pr0jectX\Px\Contracts\ExecuteTypeInterface;

/**
 * Define the execute type base class.
 */
abstract class ExecuteTypeBase implements ExecuteTypeInterface
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * The execute type constructor.
     *
     * @param array $definition
     *   An array of the execute type definition.
     */
    public function __construct(array $definition)
    {
        foreach ($definition as $property => $value) {
            if (!property_exists($this, $property)) {
                continue;
            }
            $this->{$property} = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(): bool
    {
        return isset($this->command) && !empty($this->command);
    }

    /**
     * {@inheritDoc}
     */
    public function setCommand(string $command): ExecuteTypeInterface
    {
        $this->command = $command;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions(array $options): ExecuteTypeInterface
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): array
    {
        $options = [];

        foreach ($this->options as $key => $value) {
            if (is_numeric($key)) {
                $options[$value] = null;
            } else {
                $options[$key] = $value;
            }
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function setArguments(array $arguments): ExecuteTypeInterface
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
