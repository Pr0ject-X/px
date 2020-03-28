<?php

namespace Pr0jectX\Px\HookExecuteType;

/**
 * Define the execute type interface.
 */
interface ExecuteTypeInterface
{
    /**
     * Define the execute type name.
     *
     * @return string
     *   The execute type name.
     */
    public function type(): string;

    /**
     * Build the execute type command definition.
     *
     * @param array $config
     *   An array of hook command configs.
     *
     * @return array
     *   A structured array of the command definition.
     */
    public function build(array $config): array;
}
