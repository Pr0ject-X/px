<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecutableBuilder;

/**
 * Define the executable builder base class.
 */
abstract class ExecutableBuilderBase
{
    /**
     * Set the executable.
     */
    protected const EXECUTABLE = '';

    /**
     * Set the executable option delimiter.
     */
    protected const OPTION_DELIMITER = '=';

    /** @var array  */
    protected $options = [];

    /** @var array  */
    protected $arguments = [];

    /**
     * Set the executable argument.
     *
     * @param string $argument
     *   The executable argument.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    public function setArgument(string $argument): ExecutableBuilderBase
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * Set the executable arguments.
     *
     * @param array $arguments
     *   An array of executable arguments.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    public function setArguments(array $arguments): ExecutableBuilderBase
    {
        foreach ($arguments as $argument) {
            $this->setArgument($argument);
        }

        return $this;
    }

    /**
     * Set the executable option parameter value.
     *
     * @param string $parameter
     *   The executable parameter.
     * @param string $value
     *   The executable parameter value.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    public function setOption(string $parameter, string $value): ExecutableBuilderBase
    {
        $this->options[$parameter] = $value;

        return $this;
    }

    /**
     * Set the executable option parameter values.
     *
     * @param array $options
     *   An array of option parameter values.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    public function setOptions(array $options): ExecutableBuilderBase
    {
        foreach ($options as $parameter => $value) {
            $this->setOption($parameter, $value);
        }

        return $this;
    }

    /**
     * Build the executable options.
     *
     * @return array
     */
    protected function buildOptions(): array
    {
        $options = [];
        $delimiter = static::OPTION_DELIMITER;

        foreach ($this->options as $parameter => $value) {
            $option = strpos($parameter, '-') === 0
                ? $parameter
                : "--{$parameter}";

            $options[] = "{$option}{$delimiter}\"{$value}\"";
        }

        return $options;
    }

    /**
     * Flatten the executable arguments.
     *
     * @return string
     */
    protected function flattenArguments(): string
    {
        return trim(implode(' ', $this->arguments));
    }

    /**
     * Flatten the executable options.
     *
     * @return string
     */
    protected function flattenOptions(): string
    {
        return trim(implode(' ', $this->buildOptions()));
    }

    /**
     * Define the command structure.
     *
     * @return array
     */
    protected function executableStructure(): array
    {
        return [
            static::EXECUTABLE,
            $this->flattenOptions(),
            $this->flattenArguments()
        ];
    }

    /**
     * Build the executable command string.
     *
     * @return string
     *   An fully executable command to run within a local or remote environment.
     */
    public function build(): string
    {
        return trim(implode(' ', $this->executableStructure()));
    }
}
