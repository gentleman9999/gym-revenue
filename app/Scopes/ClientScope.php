<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class ClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        //first just try checking the session if they are typical CRM Client User
        if (array_key_exists('client_id', session()->all())) {
            $builder->whereClientId(session('client_id'));

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
