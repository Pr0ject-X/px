<?php

namespace Pr0jectX\Px\ProjectX\Plugin\DeployType;

/**
 * Define the GIT deploy type interface.
 */
interface GitDeployTypeInterface
{
    /**
     * Get the GIT repository.
     *
     * @return string
     *   The the GIT repository.
     */
    public function getRepo() : string;

    /**
     * Get the GIT origin.
     *
     * @return string
     *   Return the GIT origin.
     */
    public function getOrigin() : string;

    /**
     * Get the GIT branch.
     *
     * @return string
     *   Return the GIT branch.
     */
    public function getBranch() : string;
}
