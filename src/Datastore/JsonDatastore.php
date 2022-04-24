<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Datastore;

/**
 * Define the JSON datastore based on the file systems.
 */
class JsonDatastore extends DatastoreFilesystem
{
    /**
     * The content merge flag.
     *
     * @var bool
     */
    protected $merge = false;

    /**
     * Set the transform input to be merged.
     *
     * @return self
     */
    public function merge(): self
    {
        $this->merge = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function transformInput($content): string
    {
        if ($this->merge) {
            $content = array_replace_recursive(
                $this->read(),
                $content
            );
        }

        return json_encode(
            $content,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function transformOutput($content): array
    {
        if (empty($content)) {
            return [];
        }

        return json_decode($content, true);
    }
}
