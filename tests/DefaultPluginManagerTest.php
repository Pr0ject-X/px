<?php

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\DefaultPluginManager;
use Pr0jectX\Px\Exception\PluginNotFoundException;
use Pr0jectX\Px\ProjectX\Plugin\PluginCommandRegisterInterface;
use Pr0jectX\Px\ProjectX\Plugin\PluginInterface;
use Pr0jectX\Px\PxApp;

/**
 * Define the test for the abstract default plugin manager.
 */
class DefaultPluginManagerTest extends TestCaseBase
{
    use PluginMockTrait;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Pr0jectX\Px\DefaultPluginManager  */
    protected $pluginManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->pluginManager = $this
            ->getMockBuilder(DefaultPluginManager::class)
            ->setConstructorArgs([
                PxApp::service('input'),
                PxApp::service('output'),
                PxApp::service('relativeNamespaceDiscovery')
            ])
            ->onlyMethods(['discover'])
            ->getMockForAbstractClass();

        $this->pluginManager
            ->expects($this->once())
            ->method('discover')
            ->willReturn($this->getMockPlugins());
    }

    public function testGetClassname(): void
    {
        $classname = $this
            ->pluginManager
            ->getClassname('standard');

        $this->assertTrue(
            is_subclass_of(
                $classname,
                PluginInterface::class
            )
        );

        $classname = $this
            ->pluginManager
            ->getClassname('nonexistent');

        $this->assertEmpty($classname);
        $this->assertIsString($classname);
    }

    public function testGetOptions(): void
    {
        $this->assertEquals(
            ['basic', 'command', 'standard'],
            array_keys($this->pluginManager->getOptions())
        );
    }

    public function testCreateInstance(): void
    {
        // Test that the instance configurations were passed.
        $configurations = ['one', 'two', 'three'];
        $instance = $this->pluginManager->createInstance(
            'standard',
            $configurations
        );

        $this->assertEquals($configurations, $instance->getConfigurations());
        $this->assertInstanceOf(PluginInterface::class, $instance);

        // Test that the instances are identical which means it was pulled from cache.
        $instance1 = $this->pluginManager->createInstance(
            'standard'
        );
        $instance2 = $this->pluginManager->createInstance(
            'standard'
        );
        $this->assertTrue($instance1 === $instance2);

        // Test that an unknown instance throws an exception.
        $this->expectException(PluginNotFoundException::class);
        $this->expectExceptionMessage(
            'The nonexistent plugin id was not found.'
        );
        $this->pluginManager->createInstance('nonexistent');
    }

    public function testLoadInstancesWithInterface(): void
    {
        $pluginInstances = $this->pluginManager
            ->loadInstancesWithInterface(PluginCommandRegisterInterface::class);

        $this->assertCount(1, $pluginInstances);
        $this->assertArrayHasKey('command', $pluginInstances);
    }
}
