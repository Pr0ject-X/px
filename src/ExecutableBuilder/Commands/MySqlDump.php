<?php

declare(strict_types=1);

namespace Pr0jectX\Px\ExecutableBuilder\Commands;

/**
 * Define the MySqlDump executable.
 */
class MySqlDump extends MySql
{
    protected const EXECUTABLE = 'mysqldump';

    /**
     * Set MySqlDump no tablespaces option.
     *
     * @return $this
     */
    public function noTablespaces(): self
    {
        $this->setOption('no-tablespaces');

        return $this;
    }
}
