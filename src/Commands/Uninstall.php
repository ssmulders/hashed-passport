<?php

namespace Ssmulders\HashedPassport\Commands;

use Illuminate\Console\Command;
use Ssmulders\HashedPassport\HashedPassport;
use Ssmulders\HashedPassport\Traits\HandlesEncryptedSecrets;

class Uninstall extends Command
{
    use HandlesEncryptedSecrets;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashed_passport:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrypts all the Laravel Passport client secrets.';

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
            $this->decrypt_client_secrets();
            $this->secrets_decrypted();
        }

        $this->info('Hashed-passport removal completed.');
        $this->info('');
        $this->info('You can now safely run:');
        $this->info('composer remove ssmulders/hashed-passport');
        $this->info('');
        $this->info('');
    }
}
