<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Database;

use Pr0jectX\Px\Contracts\DatabaseInterface;
use Pr0jectX\Px\PxApp;

/**
 * Define the database opener service.
 */
class DatabaseOpener
{
    /**
     * @var array
     */
    protected $applications = [];

    /**
     * Define default database application.
     */
    public const DEFAULT_APPLICATION = 'sequel_ace';

    /**
     * The database application open command.
     *
     * @param string $name
     *   The database application name.
     *
     * @return ?string
     *   The database application open command.
     */
    public function command(
        string $name,
        DatabaseInterface $database
    ): ?string {
        if (
            ($definition = $this->getApplicationDefinition($name))
            && is_callable($definition['callback'])
        ) {
            return call_user_func(
                $definition['callback'],
                $definition['location'],
                $database
            );
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The database application %s is invalid!',
                $name
            ));
        }

        return null;
    }

    /**
     * Get the database application options.
     *
     * @return array
     *   An array of the database application options.
     */
    public function applicationOptions(): array
    {
        $options = [];

        foreach ($this->discoverApplications() as $key => $info) {
            if (!isset($info['label'])) {
                continue;
            }
            $options[$key] = $info['label'];
        }

        return $options;
    }

    /**
     * The database application definitions.
     *
     * @return array[]
     *   An array of the database application definitions.
     */
    protected function applicationDefinitions(): array
    {
        return [
            'sequel_ace' => [
                'os' => 'Darwin',
                'label' => 'Sequel Ace',
                'locations' => '/Applications/Sequel Ace.app',
                'callback' => function (string $appLocation, $database) {
                    return $this->createSequelDatabaseCommand($appLocation, $database);
                }
            ],
            'sequel_pro' => [
                'os' => 'Darwin',
                'label' => 'Sequel Pro',
                'locations' => '/Applications/Sequel Pro.app',
                'callback' => function (string $appLocation, $database) {
                    return $this->createSequelDatabaseCommand($appLocation, $database);
                }
            ],
            'table_plus' => [
                'os' => 'Darwin',
                'label' => 'TablePlus',
                'locations' => [
                    '/Applications/TablePlus.app',
                    '/Applications/Setapp/TablePlus.app',
                ],
                'callback' => function (string $appLocation, $database) {
                    return $this->createTablePlusDatabaseCommand($appLocation, $database);
                }
            ],
        ];
    }

    /**
     * Get the database application definition.
     *
     * @param string $name
     *   The database application machine name.
     *
     * @return array
     *   An array of the database application definition parameters.
     */
    protected function getApplicationDefinition(string $name): array
    {
        return $this->discoverApplications()[$name] ?? [];
    }

    /**
     * Resolve the database application location.
     *
     * @param array $locations
     *   An array of searchable locations.
     *
     * @return string|null
     *   The database application location on the host file system.
     */
    protected function resolveApplicationLocation(array $locations): ?string
    {
        foreach ($locations as $location) {
            if (!file_exists($location)) {
                continue;
            }
            return $location;
        }

        return null;
    }

    /**
     * Discover the database applications.
     *
     * @return array
     *   An array of database applications found on the host system.
     */
    protected function discoverApplications(): array
    {
        if (empty($this->applications)) {
            foreach ($this->applicationDefinitions() as $key => $definition) {
                if ($definition['os'] === PHP_OS && isset($definition['locations'])) {
                    $locations = is_array($definition['locations'])
                        ? $definition['locations']
                        : [$definition['locations']];

                    if ($location = $this->resolveApplicationLocation($locations)) {
                        $this->applications[$key] = [
                            'label' => $definition['label'],
                            'callback' => $definition['callback'],
                            'location' => $location
                        ];
                    }
                }
            }
        }

        return $this->applications;
    }

    /**
     * Build the TablePlus database URL.
     *
     * @param \Pr0jectX\Px\Contracts\DatabaseInterface $database
     *   The environment database.
     *
     * @return string|null
     *   The TablePlus database URL.
     */
    protected function buildTablePlusDatabaseUrl(
        DatabaseInterface $database
    ): ?string {
        $host = "{$database->getType()}://{$database->getUsername()}";
        return "$host:{$database->getPassword()}@{$database->getHost()}:{$database->getPort()}/{$database->getDatabase()}";
    }

    /**
     * Create TablePlus database command.
     *
     * @param string $application
     *   The database application.
     * @param \Pr0jectX\Px\Contracts\DatabaseInterface $database
     *   The environment database.
     *
     * @return string|null
     *   The TablePlus database open command.
     */
    protected function createTablePlusDatabaseCommand(
        string $application,
        DatabaseInterface $database
    ): ?string {
        if (!$database->isValid()) {
            return null;
        }
        $query = http_build_query([
            'statusColor' => '007F3D',
            'enviroment' => 'local',
            'name' => 'Project-X Database',
            'tLSMode' => 0,
            'usePrivateKey' => 'true',
            'safeModeLevel' => 0,
            'advancedSafeModeLevel' => 0
        ]);
        $url = $this->buildTablePlusDatabaseUrl($database);

        return "open -a '$application' $url?$query";
    }

    /**
     * Create the sequel (pro/ace) database command.
     *
     * @param string $application
     *   The database application.
     * @param \Pr0jectX\Px\Contracts\DatabaseInterface $database
     *   The environment database.
     *
     * @return string|null
     *   The sequel database open command.
     */
    protected function createSequelDatabaseCommand(
        string $application,
        DatabaseInterface $database
    ): ?string {
        if (!$database->isValid()) {
            return null;
        }
        $sequelFile = $this->sequelWriteContents(
            $database
        );

        if (!isset($sequelFile)) {
            return null;
        }

        return "open -a '$application' $sequelFile";
    }

    /**
     * Sequel write contents.
     *
     * @param \Pr0jectX\Px\Contracts\DatabaseInterface $database
     * @return string|null
     */
    protected function sequelWriteContents(
        DatabaseInterface $database
    ): ?string {
        $contents = str_replace([
            '{label}',
            '{type}',
            '{host}',
            '{port}',
            '{database}',
            '{username}',
            '{password}',
        ], [
            'Project-X Local Database',
            $database->getType(),
            $database->getHost(),
            $database->getPort(),
            $database->getDatabase(),
            $database->getUsername(),
            $database->getPassword(),
        ], $this->sequelFileContents());

        $projectTempDir = PxApp::projectTempDir();
        $sequelFilePath = "$projectTempDir/sequel.spf";

        return file_put_contents($sequelFilePath, $contents) !== false
            ? $sequelFilePath
            : null;
    }

    /**
     * Sequel file template contents.
     *
     * @return string|null
     */
    protected function sequelFileContents(): ?string
    {
        return PxApp::loadTemplate('sequel.xml', ['database']);
    }
}
