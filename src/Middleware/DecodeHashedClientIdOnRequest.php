<?php

namespace Ssmulders\HashedPassport\Middleware;

use Closure;
use Ssmulders\HashedPassport\Traits\HashesIds;

class DecodeHashedClientIdOnRequest
{
    use HashesIds;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->offsetExists('client_id')) {
            $client_id = $request->offsetGet('client_id');

            if (!is_numeric($client_id)) {
                $result = $this->decode($client_id);

                if (count($result) > 0) {
                    $request->offsetSet('client_id', $result[0]);
                } else {
                    $request->offsetSet('client_id', -1);
                }
            }
        }

        return $next($request);
    }
}
