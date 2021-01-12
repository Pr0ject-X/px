<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Contracts;

/**
 * Define the database command interface.
 */
interface DatabaseCommandInterface
{
    /**
     * Launch the database connection.
     */
    public function dbLaunch();

    /**
     * Import the database file.
     *
     * @param string $importFile
     *   The database import file.
     * @param array $opts
     *   The database import options.
     */
    public function dbImport(string $importFile, array $opts = []);

    /**
     * Export the database file.
     *
     * @param string $exportDir
     *   The database export directory.
     * @param array $opts
     *   The database export options.
     */
    public function dbExport(string $exportDir, array $opts = []);
}
