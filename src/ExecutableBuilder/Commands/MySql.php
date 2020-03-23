<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecutableBuilder\Commands;

use Pr0jectX\Px\ExecutableBuilder\ExecutableBuilderBase;

/**
 * Define the MySql executable.
 */
class MySql extends ExecutableBuilderBase
{
    const EXECUTABLE = 'mysql';

    /**
     * Set the MySql database.
     *
     * @param string $database
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\MySql
     */
    public function database(string $database): MySql
    {
        $this->setArgument($database);

        return $this;
    }

    /**
     * Set the MySql host.
     *
     * @param string $host
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\MySql
     */
    public function host(string $host): MySql
    {
        $this->setOption(__FUNCTION__, $host);

        return $this;
    }

    /**
     * Set the MySql user.
     *
     * @param string $user
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\MySql
     */
    public function user(string $user): MySql
    {
        $this->setOption(__FUNCTION__, $user);

        return $this;
    }

    /**
     * Set the MySql password.
     *
     * @param string $password
     *
     * @return \Pr0jectX\Px\ExecutableBuilder\Commands\MySql
     */
    public function password(string $password): MySql
    {
        $this->setOption(__FUNCTION__, $password);

        return $this;
    }
}
