<?php

namespace Droath\ProjectX\ProjectX\Plugin\DeployType;

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
    public function getRepo();

    /**
     * Get the GIT origin.
     *
     * @return string
     *   Return the GIT origin.
     */
    public function getOrigin();

    /**
     * Get the GIT branch.
     *
     * @return string
     *   Return the GIT branch.
     */
    public function getBranch();
}
