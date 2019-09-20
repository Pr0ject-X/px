<?php

namespace Droath\ProjectX\Exception;

/**
 * Define the deploy type option required.
 */
class DeployTypeOptionRequired extends \RuntimeException
{
    /**
     * Deploy type options required constructor.
     *
     * @param $type
     *   The option type.
     * @param $option
     *   The option value.
     * @param int $code
     *   The exception code.
     * @param null $previous
     */
    public function __construct($type, $option, $code = 0, $previous = NULL)
    {
        $message = sprintf("The %s %s option is required!", $type, $option);

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}
