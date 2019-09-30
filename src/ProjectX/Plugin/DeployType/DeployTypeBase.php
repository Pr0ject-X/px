<?php

namespace Pr0jectX\Px\ProjectX\Plugin\DeployType;

use Pr0jectX\Px\CommonCommandTrait;
use Pr0jectX\Px\Exception\DeploymentRuntimeException;
use Pr0jectX\Px\ProjectX\Plugin\PluginTasksBase;
use Symfony\Component\Console\Question\Question;

/**
 * Define the deploy type base class.
 */
abstract class DeployTypeBase extends PluginTasksBase implements DeployTypeInterface
{
    use CommonCommandTrait;

    /**
     * Get the deploy type options.
     *
     * @return array
     *   An array of deploy type options.
     */
    public function getOptions()
    {
        return $this->input()->getOptions();
    }

    /**
     * Get the deployment build directory.
     *
     * @return string
     *   The artifact build directory.
     */
    public function getBuildDir()
    {
        return $this->getOptions()['build-dir'];
    }

    /**
     * Build the semantic version.
     *
     * @param $lastVersion
     *   The last semantic version.
     *
     * @return string
     *   The next build semantic version.
     */
    protected function buildSemanticVersion($lastVersion)
    {
        $nextVersion = $this->incrementSemanticVersion($lastVersion);

        $question = (new Question("Set build version [{$nextVersion}]: ", $nextVersion))
            ->setValidator(function ($inputVersion) use ($lastVersion) {
                $inputVersion = trim($inputVersion);

                if (version_compare($inputVersion, $lastVersion, '==')) {
                    throw new \RuntimeException(
                        'Build version has already been used.'
                    );
                }

                if (!$this->isVersionNumeric($inputVersion)) {
                    throw new \RuntimeException(
                        'Build version is not numeric.'
                    );
                }

                return $inputVersion;
            });

        return $this->doAsk($question);
    }

    /**
     * Increment the semantic version number.
     *
     * @param $version
     *   The current semantic version number.
     * @param int $patchLimit
     *   The version patch limit.
     * @param int $minorLimit
     *   The version minor limit.
     *
     * @return string
     *   The incremented semantic version.
     */
    protected function incrementSemanticVersion(
        $version,
        $patchLimit = 20,
        $minorLimit = 50
    )
    {
        if (!$this->isVersionNumeric($version)) {
            throw new \RuntimeException(
                'Unable to increment semantic version.'
            );
        }
        list($major, $minor, $patch) = explode('.', $version);

        if ($patch < $patchLimit) {
            ++$patch;
        } else if ($minor < $minorLimit) {
            ++$minor;
            $patch = 0;
        } else {
            ++$major;
            $patch = 0;
            $minor = 0;
        }

        return "{$major}.{$minor}.{$patch}";
    }

    /**
     * Compute if version is numeric.
     *
     * @param $version
     *   The version number.
     *
     * @return bool
     *   Return true if the version is numeric; otherwise false.
     */
    protected function isVersionNumeric($version)
    {
        foreach (explode('.', $version) as $part) {
            if (!is_numeric($part)) {
                return false;
            }
        }

        return true;
    }
}
