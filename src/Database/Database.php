<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Database;

use Pr0jectX\Px\Contracts\DatabaseInterface;

/**
 * Define the database object.
 */
class Database implements DatabaseInterface
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): DatabaseInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function setHost(string $host): DatabaseInterface
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function setPort(int $port): DatabaseInterface
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @inheritDoc
     */
    public function setDatabase(string $name): DatabaseInterface
    {
        $this->database = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername(string $username): DatabaseInterface
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setPassword(string $password): DatabaseInterface
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the database connection properties.
     *
     * @return array
     *   An array of the database connection details.
     */
    protected function getDatabaseProperties(): array
    {
        return get_object_vars($this);
    }
}
