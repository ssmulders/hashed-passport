<?php

namespace Ssmulders\HashedPassport\Tests;

use Illuminate\Support\Facades\DB;
use Ssmulders\HashedPassport\Traits\HashesIds;

class HashingTest extends TestCase
{
    use HashesIds;

    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createTestClient();
    }

    /** @test */
    public function adds_client_id_to_model_when_loading()
    {
        // Given
        // When
        // Then
        $this->assertArrayHasKey('client_id', $this->client->toArray());
    }

    /** @test */
    public function hashes_client_id()
    {
        // Given
        $database_client = DB::table($this->client->getTable())->where('id', '=', $this->client->id)->first();

        // When
        $this->assertNotEquals($database_client->id, $this->client->client_id);

        // And when decoded, they're the same
        $this->assertEquals($database_client->id, $this->decode($this->client->client_id)[0]);
    }
}
