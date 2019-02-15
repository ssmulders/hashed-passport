<?php

namespace Ssmulders\HashedPassport\Traits;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Collection;
use Laravel\Passport\Client;

/**
 * Trait EncryptsSecrets
 *
 * @package Ssmulders\HashedPassport
 *
 * For easy encrypting / decrypting of the oAuth client secrets.
 */
trait HandlesEncryptedSecrets
{
    /**
     * Decrypt all values back to it's plain-text version.
     */
    private function decrypt_client_secrets()
    {
        /** @var Client $oauth_client */
        foreach ($this->oauth_clients() as $oauth_client) {
            try {
                $decrypted_secret = decrypt($oauth_client->secret);
                \DB::table('oauth_clients')->where('id', $oauth_client->id)
                    ->update(['secret' => $decrypted_secret]);
            } catch (DecryptException $e) {
                $this->write_to_console('Decryption for oauth_client with id: ' . $oauth_client->id .
                                        ' FAILED. Could be it was already decrypted.');
                $this->write_to_console('Stored secret:');
                $this->write_to_console($oauth_client->secret);
            }
        }
    }

    /**
     * Walk through all current oauth_clients using the DB facade to bypass the Observer and make sure
     * that all entries are saved with encryption.
     */
    private function encrypt_client_secrets()
    {
        foreach ($this->oauth_clients() as $oauth_client) {
            \DB::table('oauth_clients')->where('id', $oauth_client->id)
                ->update(['secret' => encrypt($oauth_client->secret)]);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     |
     |
     |
     |
     */

    private function secrets_encrypted()
    {
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
        $this->write_to_console("           .---------------.           ");
        $this->write_to_console("          / .-------------. \          ");
        $this->write_to_console("         / /               \ \         ");
        $this->write_to_console("         | |               | |         ");
        $this->write_to_console("        _| |_______________| |_        ");
        $this->write_to_console("      .' |_|               |_| '.      ");
        $this->write_to_console("      '._____ ___________ _____.'      ");
        $this->write_to_console("      |     .'___________'.     |      ");
        $this->write_to_console("      '.__.'.'           '.'.__.'      ");
        $this->write_to_console("      '.    |  secrets    |    .'      ");
        $this->write_to_console("      '.__  |  encrypted  |  __.'      ");
        $this->write_to_console("      |   '.'.___________.'.'   |      ");
        $this->write_to_console("      '.____'.___________.'____.'      ");
        $this->write_to_console("      '._______________________.'      ");
        $this->write_to_console("                                       ");
        $this->write_to_console("      hashed-passport encryption active ");
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
    }

    private function secrets_decrypted()
    {
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
        $this->write_to_console("           .---------------.           ");
        $this->write_to_console("          / .-------------. \          ");
        $this->write_to_console("         / /               \ \         ");
        $this->write_to_console("         | |               | |         ");
        $this->write_to_console("         | |               | |         ");
        $this->write_to_console("         | |               |_/         ");
        $this->write_to_console("         | |                           ");
        $this->write_to_console("        _| |___________________        ");
        $this->write_to_console("      .' |_|               \_\ '.      ");
        $this->write_to_console("      '._____ ___________ _____.'      ");
        $this->write_to_console("      |     .'___________'.     |      ");
        $this->write_to_console("      '.__.'.'           '.'.__.'      ");
        $this->write_to_console("      '.    |  secrets    |    .'      ");
        $this->write_to_console("      '.__  |  decrypted  |  __.'      ");
        $this->write_to_console("      |   '.'.___________.'.'   |      ");
        $this->write_to_console("      '.____'.___________.'____.'      ");
        $this->write_to_console("      '._______________________.'      ");
        $this->write_to_console("                                       ");
        $this->write_to_console("      hashed-passport encryption deactivated   ");
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
        $this->write_to_console("                                       ");
    }

    /**
     * Writes to the console like an Artisan Command.
     *
     * @param $message
     */
    private function write_to_console($message)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();

        $output->writeln($message);
    }

    /*
     |--------------------------------------------------------------------------
     | Getters and Setters
     |--------------------------------------------------------------------------
     |
     |
     |
     |
     */

    /**
     * @var $oauth_clients Collection containing all the oauth clients.
     */
    protected $oauth_clients;

    /**
     * Gets clients
     *
     * @return mixed
     */
    protected function oauth_clients()
    {
        return \DB::table('oauth_clients')->get();
    }
}
