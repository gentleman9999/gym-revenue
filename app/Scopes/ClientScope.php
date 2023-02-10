<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Support\CurrentInfoRetriever;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     */
    public function apply(Builder $builder, Model $_model): void
    {
        //first just try checking the session if they are typical CRM Client User
        if (array_key_exists('client_id', session()->all())) {
            $builder->whereClientId(CurrentInfoRetriever::getCurrentClientID());

            return;
        }

        //if no session keys set, must be API - so check the request body
        $request = request();
        if ((auth()->user() || $request->bearerToken()) && $request->client_id ?? false) {
            //must be API
            $builder->whereClientId($request->client_id);
        }
    }
}
