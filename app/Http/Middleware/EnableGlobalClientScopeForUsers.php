<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Scopes\ClientScope;
use Closure;

class EnableGlobalClientScopeForUsers
{
    //Adds a global ClientScope to User after all of the auth stuff middleware is complete
    public function handle($request, Closure $next)
    {
        User::addGlobalScope(new ClientScope());

        return $next($request);
    }
}
