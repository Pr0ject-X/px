<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\EnvironmentTypePluginManager;
use Pr0jectX\Px\ProjectX\Plugin\EnvironmentType\LocalhostEnvironmentType;
use Pr0jectX\Px\PxApp;

/**
 * Define the test for the environment type plugin manager.
 */
class EnvironmentTypePluginManagerTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\EnvironmentTypePluginManager
     */
    protected $pluginManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->pluginManager = new EnvironmentTypePluginManager(
            PxApp::service('input'),
            PxApp::service('output'),
            PxApp::service('relativeNamespaceDiscovery')
        );
    }

    public function testDiscover(): void
    {
        $classes = $this->pluginManager->discover();
        $this->assertTrue(
            in_array(LocalhostEnvironmentType::class, $classes)
        );
    }
}
