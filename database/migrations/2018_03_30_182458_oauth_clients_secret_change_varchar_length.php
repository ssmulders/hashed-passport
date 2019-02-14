<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OauthClientsSecretChangeVarcharLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Change the VARCHAR to 2048 to future proof it, and make sure all encrypted strings can be saved
         */
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('secret', 2048)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * Change the value back to it's default value of 100
         */
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('secret', 100)->change();
        });
    }
}
