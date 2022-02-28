<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecutableBuilder\Commands;

use Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase;

/**
 * Define the scp executable.
 */
class Scp extends ExecutableBuilderBase
{
    protected const EXECUTABLE = 'scp';

    protected const OPTION_DELIMITER = ' ';

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
     * Set the scp port.
     *
     * @param int $port
     *   The ssh port value.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function port(int $port): Scp
    {
        $this->setOption('-P', $port);

        return $this;
    }

    /**
     * Set the scp identify file path.
     *
     * @param string $identifyFile
     *   The path to the ssh identify file.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function identityFile(string $identifyFile): Scp
    {
        if (!file_exists($identifyFile)) {
            throw new \RuntimeException(
                sprintf('Unable to locate the SSH identify file %s', $identifyFile)
            );
        }
        $this->setOption('-i', $identifyFile);

        return $this;
    }

    /**
     * Set the scp identities only option.
     *
     * @param bool $value
     *   Set value to TRUE if identities should only used, otherwise FALSE.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function identitiesOnly(bool $value): Scp
    {
        $value = $value ? 'yes' : 'no';
        $this->setOption('-o', "IdentitiesOnly={$value}");

        return $this;
    }

    /**
     * Set the scp strict host key checking option.
     *
     * @param bool $value
     *   Set value to TRUE if identities should only used, otherwise FALSE.
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\Scp
     */
    public function strictHostKeyChecking(bool $value): self
    {
        $value = $value ? 'yes' : 'no';
        $this->setOption('-o', "StrictHostKeyChecking={$value}");

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
