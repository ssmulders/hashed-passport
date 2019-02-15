<?php

namespace Ssmulders\HashedPassport\Traits;

use Vinkla\Hashids\Facades\Hashids;

/**
 * Trait HashesIds
 *
 * @package Ssmulders\HashedPassport
 *
 * Uses the configured salt to hash and unhash the Passport Client's ID
 */
trait HashesIds
{
    /**
     * Hashes the regular incrementing integer id
     *
     * @param $id
     *
     * @return string
     */
    protected function encode($id)
    {
        return Hashids::connection('hashed_passport')->encode($id);
    }

    /**
     * UnHashes the hashed client_id into the auto-incrementing integer
     *
     * @param $client_id
     *
     * @return mixed
     */
    protected function decode($client_id)
    {
        return Hashids::connection('hashed_passport')->decode($client_id);
    }
}
