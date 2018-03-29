<?php

namespace Ssmulders\HashedPassport\Traits;

use Hashids\Hashids;

/**
 * Trait HashesIds
 * @package Ssmulders\HashedPassport
 *
 * Uses the APP_KEY to hash and unhash the Passport Client's ID
 */
trait HashesIds {

    /**
     * @var Hashids $hashIds
     */
    protected $hashIds;

    public function __construct()
    {
        $this->hashIds = new Hashids(env('APP_KEY'));
    }

    /**
     * Hashes the regular incrementing integer id
     *
     * @param $id
     * @return string
     */
    protected function encode($id)
    {
        return \Hashids::connection('client_id')->encode($id);
    }

    /**
     * UnHashes the hashed client_id into the auto-incrementing integer
     *
     * @param $client_id
     * @return mixed
     */
    protected function decode($client_id)
    {
        return \Hashids::connection('client_id')->decode($client_id);
    }

}