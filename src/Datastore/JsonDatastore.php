<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Datastore;

/**
 * Define the JSON datastore based on the file systems.
 */
class JsonDatastore extends DatastoreFilesystem
{
    /**
     * {@inheritDoc}
     */
    protected function transformInput($content): string
    {
        return json_encode(
            $content,
            JSON_PRETTY_PRINT
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
