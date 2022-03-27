<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Contracts;

/**
 * Define the database interface.
 */
interface DatabaseInterface
{
    /**
     * Determine if the database is valid.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Get the database type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set the database type.
     *
     * @param string $type
     *
     * @return \Pr0jectX\Px\Database\Database
     */
    public function setType(string $type): self;

    /**
     * Get the database host address.
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Set the database host address.
     *
     * @param string $host
     *
     * @return \Pr0jectX\Px\Database\Database
     */
    public function setHost(string $host): self;

    /**
     * Get the database port.
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * Set the database port.
     *
     * @param int $port
     *
     * @return \Pr0jectX\Px\Database\Database
     */
    public function setPort(int $port): self;

    /**
     * Get the database name.
     *
     * @return string
     */
    public function getDatabase(): string;

    /**
     * Set the database name.
     *
     * @param string $name
     *
     * @return \Pr0jectX\Px\Database\Database
     */
    public function setDatabase(string $name): self;

    /**
     * Get the database username.
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Set the database username.
     *
     * @param string $username
     *
     * @return \Pr0jectX\Px\Database\Database
     */
    public function setUsername(string $username): self;

    /**
     * Get the database password.
     *
     * @return string
     */
    public function getPassword(): string;

    /**
     * Set the database password.
     *
     * @param string $password
     *
     * @return \Pr0jectX\Px\Database\Database
     */
    public function setPassword(string $password): self;
}
