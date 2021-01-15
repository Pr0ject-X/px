<?php

declare(strict_types=1);

namespace Pr0jectX\Px;

use Consolidation\Config\Util\ConfigOverlay;
use Symfony\Component\Yaml\Yaml;

/**
 * Define the configuration command trait.
 */
trait ConfigurationCommandTrait
{
    /**
     * Write project-x configurations to the filesystem.
     *
     * @param array $data
     *   An array of data to add.
     *
     * @return bool
     *   Return true if the configuration was saved; otherwise false.
     */
    protected function writeConfiguration(array $data): bool
    {
        $projectRoot = PxApp::projectRootPath();
        $configFilename = PxApp::CONFIG_FILENAME;

        $configuration = PxApp::getConfiguration()
            ->getContext(ConfigOverlay::DEFAULT_CONTEXT)
            ->combine($data)
            ->export();

        $results = $this->taskWriteToFile("{$projectRoot}/{$configFilename}.yml")
            ->text(Yaml::dump($configuration, 10, 4))
            ->run();

        return $results->wasSuccessful();
    }
}
