<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Datastore;

use Symfony\Component\Yaml\Yaml;

/**
 * Define the Yaml datastore based on the file systems.
 */
class YamlDatastore extends DatastoreFilesystem
{
    /**
     * Yaml dump inline value.
     *
     * @var int
     */
    protected $inline = 2;

    /**
     * Yaml dump indent value.
     *
     * @var int
     */
    protected $indent = 4;

    /**
     * Set the yaml dump inline value.
     *
     * @param int $inline
     *   The inline integer.
     *
     * @return $this
     */
    public function setInline(int $inline): self
    {
        $this->inline = $inline;

        return $this;
    }

    /**
     * Set the yaml dump indent value.
     *
     * @param int $indent
     *   The indent integer.
     *
     * @return $this
     */
    public function setIndent(int $indent): self
    {
        $this->indent = $indent;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function transformInput($content): string
    {
        return Yaml::dump($content, $this->inline, $this->indent);
    }

    /**
     * {@inheritDoc}
     */
    protected function transformOutput($content): array
    {
        if (empty($content)) {
            return [];
        }

        return Yaml::parse($content);
    }
}
