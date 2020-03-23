<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\CommandTypePluginManager;
use Pr0jectX\Px\PxApp;

/**
 * Define the test for the command type plugin manager.
 */
class CommandTypePluginManagerTest extends TestCaseBase
{
    /**
     * @var \Pr0jectX\Px\CommandTypePluginManager
     */
    protected $pluginManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->pluginManager = new CommandTypePluginManager(
            PxApp::service('input'),
            PxApp::service('output'),
            PxApp::service('relativeNamespaceDiscovery')
        );
    }

    public function testDiscover(): void
    {
        $classes = $this->pluginManager->discover();
        $this->assertEmpty($classes);
    }
}
