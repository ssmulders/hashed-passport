<?php

namespace Ssmulders\HashedPassport\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
    protected $description = 'Finishes the hashed-passport installation by encrypting all the client secrets.';

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
        if(HashedPassport::$withEncryption)
        {
            /**
             * Just calls the migration ;-)
             */
            Artisan::call('migrate');

            $this->encrypt_client_secrets();
            $this->secrets_encrypted();
        }

        $this->info('Hashed passport installation completed.');
    }


}
