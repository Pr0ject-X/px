<?php

declare(strict_types=1);

namespace Pr0jectX\Px\Tests;

use Pr0jectX\Px\Datastore\JsonDatastore;
use Pr0jectX\Px\PxApp;

/**
 * Define the JSON datastore test.
 */
class JsonDatastoreTest extends TestCaseBase
{
    protected $jsonDatastore;

    public function setUp(): void
    {
        parent::setUp();

        $this->jsonDatastore = new JsonDatastore(
            PxApp::projectTempDir() . '/state/data.json'
        );
    }

    public function testRead()
    {
        $data = $this->jsonDatastore->read();
        $this->assertEmpty($data);
        $this->assertIsArray($data);
    }

    public function testWrite()
    {
        $data = [
            'item' => 'This is content',
        ];
        $this->assertTrue(
            $this->jsonDatastore->write($data)
        );
        $this->assertEquals(
            $data,
            $this->jsonDatastore->read()
        );
    }
}
