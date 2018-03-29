<?php

namespace Ssmulders\HashedPassport\Middleware;

use Closure;
use Ssmulders\HashedPassport\Traits\HashesIds;

class HashClientIdOnResponse
{
    use HashesIds;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        /**
         * Turn the JSON string into an array for easy processing
         */
        $clients = json_decode($response->content(), TRUE);

        /**
         * Send the default response when
         *
         * It's not an array (for whatever reason)
         * The array isn't multi-dimensional (which it would be if all keys are returning)
         */
        if(!is_array($clients) || ! array_key_exists(0, $clients))
        {
            return $response;
        }

        /**
         * Return the updated Clients
         */
        return response()->json($this->hash_ids_of($clients));
    }

    /**
     * Loop through the Passport Client data and insert the hashed client_id
     *
     * @param $clients
     * @return mixed
     */
    private function hash_ids_of($clients)
    {
        foreach ($clients as $index => $client)
        {
            $clients[$index]['client_id'] = $this->encode($clients[$index]['id']);
        }

        return $clients;
    }
}
