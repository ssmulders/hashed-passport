<?php

namespace Ssmulders\HashedPassport\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ssmulders\HashedPassport\Traits\HandlesEncryptedSecrets;

class Install extends Command
{
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
        /**
         * Just calls the migration ;-)
         */
        Artisan::call('migrate');

        $this->info('Hashed-pass installation completed.');
    }


}
