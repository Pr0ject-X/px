<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Datastore;

/**
 * Define the datastore file interface.
 */
interface DatastoreFilesystemInterface
{
    /**
     * Read the contents from the filesystem.
     */
    public function read();

    /**
     * Write the content to the filesystem.
     *
     * @param $content
     *   The content to write to the filesystem.
     *
     * @return bool
     *   Return true when the write was successful; otherwise false.
     */
    public function write($content): bool;
}
