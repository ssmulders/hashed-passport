<?php

namespace Ssmulders\HashedPassport\Tests;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use Ssmulders\HashedPassport\HashedPassportServiceProvider;
use Vinkla\Hashids\HashidsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    protected function getPackageProviders($app)
    {
        return [
            PassportServiceProvider::class,
            HashidsServiceProvider::class,
            HashedPassportServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Generate encryption key
        $app['config']->set('app.key', 'base64:' . base64_encode(Encrypter::generateKey($app['config']['app.cipher'])));

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function createTestClient()
    {
        return Passport::client()->create([
            'name'                   => 'Test Client',
            'secret'                 => Str::random(40),
            'redirect'               => '',
            'personal_access_client' => false,
            'password_client'        => false,
            'revoked'                => false,
        ])->fresh();
    }
}
