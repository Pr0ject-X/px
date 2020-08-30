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
     * Set the executable default option quote.
     */
    protected const OPTION_QUOTE = '"';

    /**
     * Set the executable default option delimiter.
     */
    protected const OPTION_DELIMITER = '=';

    /** @var array  */
    protected $options = [];

    /** @var array  */
    protected $arguments = [];

    /** @var array  */
    protected $configOptions = [];

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
     * @param $parameter
     *   The executable parameter.
     * @param $value
     *   The executable parameter value.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    public function setOption($parameter, $value = null): ExecutableBuilderBase
    {
        if (isset($value) && !is_scalar($value)) {
            throw new \InvalidArgumentException(
                'Invalid option value. Only scalar values are allowed.'
            );
        }
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
            $parameter = !is_numeric($parameter)
                ? $parameter
                : $value;

            $value = $parameter !== $value
                ? $value
                : null;

            $this->setOption($parameter, $value);
        }

        return $this;
    }

    /**
     * Set the configuration options.
     *
     * @param array $configOptions
     *   An array of the configuration options.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase
     */
    public function setConfigOptions(array $configOptions): ExecutableBuilderBase
    {
        $this->configOptions = $configOptions;

        return $this;
    }

    /**
     * Get all configuration options.
     *
     * @return array|\string[]
     */
    protected function getConfigOptions(): array
    {
        return $this->configOptions + [
            'quote' => static::OPTION_QUOTE,
            'delimiter' => static::OPTION_DELIMITER,
        ];
    }

    /**
     * Get a single configuration option.
     *
     * @param string $name
     *   The name of the configuration property.
     *
     * @return mixed|string
     */
    protected function getConfigOption(string $name)
    {
        return $this->getConfigOptions()[$name];
    }

    /**
     * Build the executable options.
     *
     * @return array
     */
    protected function buildOptions(): array
    {
        $options = [];

        $quote = $this->getConfigOption('quote');
        $delimiter = $this->getConfigOption('delimiter');

        foreach ($this->options as $parameter => $value) {
            $parameter = strpos($parameter, '-') === 0
                ? $parameter
                : "--{$parameter}";

            if (isset($value) && !empty($value)) {
                $parameter .= "{$delimiter}{$quote}{$value}{$quote}";
            }

            $options[] = $parameter;
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
