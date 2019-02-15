<?php

namespace Ssmulders\HashedPassport\Commands;

use Illuminate\Console\Command;
use Ssmulders\HashedPassport\HashedPassport;
use Ssmulders\HashedPassport\Traits\HandlesEncryptedSecrets;

class Install extends Command
{
    use HandlesEncryptedSecrets;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashed_passport:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypts all the Laravel Passport client secrets.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (HashedPassport::$withEncryption) {
            $this->encrypt_client_secrets();
            $this->secrets_encrypted();
        }

        $this->info('Hashed Passport installation completed.');
        $this->info('');
        $this->info('');
    }
}
