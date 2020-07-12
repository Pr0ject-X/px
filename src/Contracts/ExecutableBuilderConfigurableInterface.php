<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Contracts;

/**
 * Define the executable builder configurable interface.
 */
interface ExecutableBuilderConfigurableInterface
{
    /**
     * Executable builder configuration options.
     *
     * @return array
     *   An array of configuration options, such as delimiter and quote.
     */
    public function execBuilderOptions(): array;
}
