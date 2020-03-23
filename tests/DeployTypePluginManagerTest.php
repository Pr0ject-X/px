<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\DeployTypePluginManager;
use Pr0jectX\Px\ProjectX\Plugin\DeployType\GitDeployType;
use Pr0jectX\Px\PxApp;

/**
 * Define the test for the deploy type plugin manager.
 */
class DeployTypePluginManagerTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\DeployTypePluginManager
     */
    protected $pluginManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->pluginManager = new DeployTypePluginManager(
            PxApp::service('input'),
            PxApp::service('output'),
            PxApp::service('relativeNamespaceDiscovery')
        );
    }

    public function testDiscover(): void
    {
        $classes = $this->pluginManager->discover();
        $this->assertTrue(
            in_array(GitDeployType::class, $classes)
        );
    }
}
