<?php

namespace Pr0jectX\Px\ProjectX\Plugin\DeployType;

/**
 * Define the deployment type interface.
 */
interface DeployTypeInterface
{
    /**
     * Run the deployment steps.
     */
    public function deploy();

    /**
     * Get the deploy type options.
     *
     * @return array
     *   An array of deploy type options.
     */
    public function getOptions();

    /**
     * Set deploy command options.
     *
     * @return array
     *   An array of \Symfony\Component\Console\Input\InputOption.
     */
    public static function deployOptions();
}
