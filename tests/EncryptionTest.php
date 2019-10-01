<?php

namespace Ssmulders\HashedPassport\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Ssmulders\HashedPassport\HashedPassport;
use Ssmulders\HashedPassport\Traits\HandlesEncryptedSecrets;

class EncryptionTest extends TestCase
{
    use DatabaseTransactions;
    use HandlesEncryptedSecrets;

    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createTestClient();
    }

    /** @test */
    public function database_secret_is_stored_encrypted()
    {
        // Given
        $database_client = DB::table($this->client->getTable())->where('id', '=', $this->client->id)->first();

        $application_secret = $this->client->secret;
        $database_secret = $database_client->secret;

        // Checks with and without encryption
        // When disabled in AppServiceProvider it uses the else clause.
        if (HashedPassport::$withEncryption) {
            // Make sure that what's stored in the DB isn't the same as what's being used in the application
            $this->assertNotSame($application_secret, $database_secret);

            // Then when we decrypt the secret, it is the same.
            $this->assertSame($application_secret, decrypt($database_secret));
        } else {
            $this->assertSame($application_secret, $database_secret);
        }
    }

    /** @test */
    public function enables_encryption_at_will()
    {
        // Given
        $hashed_passport = HashedPassport::withEncryption();

        // Then
        $this->assertTrue($hashed_passport::$withEncryption);
    }
}
