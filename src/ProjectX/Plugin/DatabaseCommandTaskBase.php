<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin;

use Pr0jectX\Px\Contracts\DatabaseCommandInterface;
use Pr0jectX\Px\Contracts\DatabaseInterface;
use Pr0jectX\Px\Database\DatabaseOpener;

/**
 * The abstract database command task class.
 */
abstract class DatabaseCommandTaskBase extends PluginCommandTaskBase implements DatabaseCommandInterface
{
    /**
     * Open the database connection using an external application.
     *
     * @param string|null $appName
     *   The DB application name e.g (table_plus, sequel_pro, sequel_ace).
     */
    public function dbLaunch(string $appName = null): void
    {
        try {
            $databaseOpener = new DatabaseOpener();
            $appOptions = $databaseOpener->applicationOptions();

            if (empty($appOptions)) {
                throw new \RuntimeException(
                    'There are no supported database applications found!'
                );
            }

            if (!isset($appName)) {
                $appDefault = array_key_exists(DatabaseOpener::DEFAULT_APPLICATION, $appOptions)
                    ? DatabaseOpener::DEFAULT_APPLICATION
                    : array_key_first($appOptions);

                $appName = count($appOptions) === 1
                    ? array_key_first($appOptions)
                    : $this->askChoice(
                        'Select the database application to launch',
                        $appOptions,
                        $appDefault
                    );
            }
            $command = $databaseOpener->command(
                $appName,
                $this->createLaunchDatabase()
            );
            $this->taskExec($command)->run();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Import a database into the environment.
     *
     * @param string $importFile
     *   The database import file.
     * @param array $opts
     *   The database import options.
     *
     * @option string $host
     *   Set the database host (defaults to database if negated).
     * @option string $host
     *   Set the database type (defaults to database if negated).
     * @option string $database
     *   Set the database table (defaults to database if negated).
     * @option string $username
     *   Set the database username (defaults to database if negated).
     * @option string $password
     *   Set the database password (defaults to database if negated).
     * @option string $env_type
     *   Set the database environment type, (e.g. primary and secondary).
     */
    public function dbImport(string $importFile, array $opts = [
        'host' => null,
        'type' => 'mysql',
        'database' => null,
        'username' => null,
        'password' => null,
        'env_type' => 'primary',
    ]): void
    {
        try {
            if (!file_exists($importFile)) {
                throw new \InvalidArgumentException(
                    'The database import file does not exist.'
                );
            }
            $database = $this->createDatabase($opts);

            if (!isset($database)) {
                throw new \InvalidArgumentException(
                    'Invalid database configuration have been provided.'
                );
            }

            $this->importDatabase(
                $database->getHost(),
                $database->getDatabase(),
                $database->getUsername(),
                $database->getPassword(),
                $importFile,
            );
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Export the database from the environment.
     *
     * @param string $exportDir
     *   The local export directory.
     * @param array $opts
     *   The database export options.
     *
     * @option string $host
     *   Set the database host (defaults to database type if negated).
     * @option string $database
     *   Set the database table (defaults to database type if negated).
     * @option string $username
     *   Set the database username (defaults to database type if negated).
     * @option string $password
     *   Set the database password (defaults to database type if negated).
     * @option string $filename
     *   The database export filename.
     * @option string $env_type
     *   Set the database environment type (e.g. primary and secondary).
     */
    public function dbExport(string $exportDir, array $opts = [
        'host' => null,
        'type' => 'mysql',
        'database' => null,
        'username' => null,
        'password' => null,
        'filename' => 'db',
        'env_type' => 'primary',
    ]): void
    {
        try {
            if (!is_dir($exportDir)) {
                throw new \InvalidArgumentException(
                    'The database export directory does not exist.'
                );
            }
            $database = $this->createDatabase($opts);

            if (!isset($database)) {
                throw new \InvalidArgumentException(
                    'Invalid database configuration have been provided.'
                );
            }
            $exportFile = "{$exportDir}/{$opts['filename']}";

            $this->exportDatabase(
                $database->getHost(),
                $database->getDatabase(),
                $database->getUsername(),
                $database->getPassword(),
                $exportFile
            );
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Run the database import process.
     *
     * @param string $host
     *   The database host.
     * @param string $database
     *   The database name.
     * @param string $username
     *   The database user.
     * @param string $password
     *   The database user password.
     * @param string $importFile
     *   The database import file path.
     */
    abstract protected function importDatabase(
        string $host,
        string $database,
        string $username,
        string $password,
        string $importFile
    ): void;

    /**
     * Run the database export process.
     *
     * @param string $host
     *   The database host.
     * @param string $database
     *   The database name.
     * @param string $username
     *   The database user.
     * @param string $password
     *   The database user password.
     * @param string $exportFile
     *   The database export filename.
     */
    abstract protected function exportDatabase(
        string $host,
        string $database,
        string $username,
        string $password,
        string $exportFile
    ): void;

    /**
     * Create the launch database instance.
     *
     * @return \Pr0jectX\Px\Contracts\DatabaseInterface|null
     */
    abstract protected function createLaunchDatabase(): ?DatabaseInterface;

    /**
     * Create the import/export database instance.
     *
     * @param array $config
     *   An array of database configuration.
     *
     * @return \Pr0jectX\Px\Contracts\DatabaseInterface|null
     */
    abstract protected function createDatabase(array $config): ?DatabaseInterface;

    /**
     * Determine if the file is gzipped.
     *
     * @param string $filepath
     *   The fully qualified path to the file.
     *
     * @return bool
     *   Return true if filepath is gzipped; otherwise false.
     */
    protected function isGzipped(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException(
                'The file path does not exist.'
            );
        }
        $contentType = mime_content_type($filepath);

        $mimeType = substr(
            $contentType,
            strpos($contentType, '/') + 1
        );

        return $mimeType === 'x-gzip' || $mimeType === 'gzip';
    }
}
