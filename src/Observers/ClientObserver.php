<?php

namespace Ssmulders\HashedPassport\Observers;

use Laravel\Passport\Client;
use Ssmulders\HashedPassport\HashedPassport;
use Ssmulders\HashedPassport\Traits\HashesIds;

class ClientObserver
{
    use HashesIds;

    /**
     * Listen to the Client retrieved event and add the hashed client_id parameter
     * and decrypt the secret.
     *
     * @param  \Laravel\Passport\Client $oauth_client
     *
     * @return void
     */
    public function retrieved(Client $oauth_client)
    {

        $oauth_client->setAttribute('client_id', $this->encode($oauth_client->getAttribute('id')));
        $oauth_client->setAttribute('client_id', $this->encode($oauth_client->getAttribute('id')));

        if (HashedPassport::$withEncryption) {
            $oauth_client->setAttribute('secret', decrypt($oauth_client->getAttribute('secret')));
        }
    }

    /**
     * Encrypt the Client Secret before storing it.
     *
     * @param Client $oauth_client
     */
    public function saving(Client $oauth_client)
    {
        // Prevent trying to save hashed client ID to the database.
        $oauth_client->offsetUnset('client_id');

        if (HashedPassport::$withEncryption) {
            $oauth_client->setAttribute('secret', encrypt($oauth_client->getAttribute('secret')));
        }
    }

    /**
     * Hash the Client ID and encrypt secret after saving.
     *
     * @param Client $oauth_client
     */
    public function saved(Client $oauth_client)
    {
        $this->retrieved($oauth_client);
    }
}
