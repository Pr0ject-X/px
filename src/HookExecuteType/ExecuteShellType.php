<?php

declare(strict_types=1);

namespace Pr0jectX\Px\HookExecuteType;

use Robo\Task\Base\Exec;

/**
 * Define the shell execute type.
 */
class ExecuteShellType extends ExecuteTypeBase implements ExecuteTypeInterface
{
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return 'shell';
    }

    /**
     * @inheritDoc
     */
    public function build(array $config): array
    {
        return [
            'command' => $config['command'],
            'options' => $config['options'] ?? [],
            'arguments' => $config['arguments'] ?? [],
            'classname' => Exec::class,
        ];
    }
}
