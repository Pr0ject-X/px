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
     * @return bool|int
     *   The function returns the number of bytes that were written to the file, or false on failure.
     */
    public function save($filename = null)
    {
        $projectRoot = PxApp::projectRootPath();
        $configFilename = PxApp::CONFIG_FILENAME;

        $configFilename = isset($filename)
            ? $filename
            : "{$configFilename}.yml";

        return file_put_contents(
            "{$projectRoot}/{$configFilename}",
            Yaml::dump($this->processConfig(), 10, 4)
        );
    }

    /**
     * Get the filtered configuration export.
     *
     * @param array $filter
     *   An array of keys to filter.
     *
     * @return array
     *   An array of filtered configurations.
     */
    protected function filterExport(array $filter)
    {
        return array_filter($this->export(), function($key) use ($filter) {
            return !in_array($key, $filter);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Process the project-x configuration.
     *
     * @return array
     *   An array of processed configuration.
     */
    protected function processConfig()
    {
        $config = $this->filterExport(
            ['options']
        );
        ksort($config);

        return $config;
    }
}
