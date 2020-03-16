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
     * @param string $import_file
     *   The fully qualified database path to the import file.
     */
    public function dbImport(string $import_file);

    /**
     * Export the database file.
     *
     * @param string $export_dir
     *   The export directory.
     * @param array $opts
     *   An array of database export options.
     */
    public function dbExport(string $export_dir, array $opts = []);
}
