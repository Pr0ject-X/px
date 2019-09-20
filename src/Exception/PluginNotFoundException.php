<?php

namespace Droath\ProjectX\Exception;

/**
 * Define the plugin id not found exception.
 */
class PluginNotFoundException extends \Exception
{
    /**
     * Plugin not found exception.
     *
     * @param $id
     *   The plugin identifier.
     * @param int $code
     *   The exception exit code.
     * @param null $previous
     */
    public function __construct($id, $code = 0, $previous = NULL)
    {
        $message = sprintf(
            "The %s plugin id was not found.", $id
        );
        parent::__construct($message, $code, $previous);
    }
}
