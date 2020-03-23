<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecutableBuilder\Commands;

use Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase;

/**
 * Define the scp executable.
 */
class Scp extends ExecutableBuilderBase
{
    const EXECUTABLE = 'scp';

    const OPTION_DELIMITER = ' ';

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $target;

    /**
     * Define the scp source path.
     *
     * @param $path
     *   The source path.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function source($path): Scp
    {
        $this->source = $path;

        return $this;
    }

    /**
     * Define the scp target path.
     *
     * @param $path
     *   The target path.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function target($path): Scp
    {
        $this->target = $path;

        return $this;
    }

    /**
     * Set the scp identify file path.
     *
     * @param string $identify_file
     *   The path to the ssh identify file.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function identityFile(string $identify_file): Scp
    {
        if (!file_exists($identify_file)) {
            throw new \RuntimeException(
                sprintf('Unable to locate the SSH identify file %s', $identify_file)
            );
        }
        $this->setOption('-i', $identify_file);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function build(): string
    {
        $executable = static::EXECUTABLE;
        return "{$executable} {$this->flattenOptions()} {$this->source}  {$this->target}";
    }
}
