<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ProjectX\Plugin\EnvironmentType;

/**
 * Define the environment database object.
 */
class EnvironmentDatabase
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * Get the database type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the database type.
     *
     * @param string $type
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function setType(string $type): EnvironmentDatabase
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the database host address.
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set the database host address.
     *
     * @param string $host
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function setHost(string $host): EnvironmentDatabase
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the database port.
     *
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Set the database port.
     *
     * @param int $port
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function setPort(int $port): EnvironmentDatabase
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the database name.
     *
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * Set the database name.
     *
     * @param string $name
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function setDatabase(string $name): EnvironmentDatabase
    {
        $this->database = $name;

        return $this;
    }

    /**
     * Get the database username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the database username.
     *
     * @param string $username
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function setUsername(string $username): EnvironmentDatabase
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the database password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the database password.
     *
     * @param string $password
     *
     * @return \Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\EnvironmentDatabase
     */
    public function setPassword($password): EnvironmentDatabase
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Determine if the database is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this->getDatabaseProperties() as $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the database connection properties.
     *
     * @return array
     *   An array of the database connection details.
     */
    public function getDatabaseProperties(): array
    {
        return get_object_vars($this);
    }
}
