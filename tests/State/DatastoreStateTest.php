<?php

namespace State;

use Pr0jectX\Px\Datastore\JsonDatastore;
use Pr0jectX\Px\PxApp;
use Pr0jectX\Px\State\DatastoreState;
use Pr0jectX\Px\Tests\TestCaseBase;

class DatastoreStateTest extends TestCaseBase
{
    /**
     * @var array
     */
    protected $nestedItems;

    /**
     * @var \Pr0jectX\Px\State\DatastoreState
     */
    protected $datastoreState;

    public function setUp(): void
    {
        parent::setUp();

        $this->nestedItems = ['nested' => [
            'value1',
            'value2',
            'value3'
        ]];

        $jsonDatastore = new JsonDatastore(
            PxApp::projectTempDir() . '/state/environment.json'
        );
        $this->datastoreState = new DatastoreState($jsonDatastore);
    }

    public function testSet()
    {
        $this->datastoreState->set(
            'items',
            $this->nestedItems
        );

        $this->assertEquals(
            $this->nestedItems,
            $this->datastoreState->get('items')
        );
    }

    public function testGet()
    {
        $this->datastoreState->set(
            'items',
            $this->nestedItems
        );
        $this->assertEquals(
            $this->nestedItems['nested'],
            $this->datastoreState->get(['items', 'nested'])
        );
    }

    public function testSave()
    {
        $this->assertTrue($this->datastoreState->save());
    }

    public function testDel()
    {
        $this->datastoreState->set(
            'items',
            $this->nestedItems
        );
        $this->datastoreState->set(
            'status',
            'running'
        );
        $this->datastoreState->del('items');

        $this->assertEquals(
            ['status' => 'running'],
            $this->datastoreState->get()
        );
    }
}
