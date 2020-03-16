<?php

namespace Pr0jectX\Px\Config;

use Pr0jectX\Px\PxApp;
use Symfony\Component\Yaml\Yaml;

/**
 * Define the project-x configuration.
 */
class Config extends \Robo\Config\Config
{
    /**
     * Save the project-x configuration.
     *
     * @param null $filename
     *   An alternative configuration filename
     *
     * @return bool
     *   The function returns the number of bytes that were written to the file, or false on failure.
     */
    public function save($filename = null) : bool
    {
        $projectRoot = PxApp::projectRootPath();
        $configFilename = PxApp::CONFIG_FILENAME;

        $configFilename = isset($filename)
            ? $filename
            : "{$configFilename}.yml";

        return file_put_contents(
            "{$projectRoot}/{$configFilename}",
            Yaml::dump($this->export(), 10, 4)
        ) !== false;
    }
}
