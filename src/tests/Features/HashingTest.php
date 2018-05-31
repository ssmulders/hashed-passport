<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Ssmulders\HashedPassport\HashedPassport;
use Ssmulders\HashedPassport\Traits\HashesIds;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HashingTest extends TestCase
{
    use HashesIds;

    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = Client::find(1);
    }

    /** @test **/
    public function adds_client_id_to_model_when_loading()
    {
        // Given
        // When
        // Then
        $this->assertArrayHasKey('client_id', $this->client->toArray());
    }

    /** @test **/
    public function hashes_client_id()
    {
        // Given
        $database_client = DB::table($this->client->getTable())->where('id', '=', 1)->first();

        // When
        $this->assertTrue($database_client->id !== $this->client->client_id);

        // And when decoded, they're the same
        $this->assertTrue($database_client->id === $this->decode($this->client->client_id)[0]);
    }

}
