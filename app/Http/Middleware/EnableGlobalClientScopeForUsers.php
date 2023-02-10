<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Users\Models\User;
use App\Scopes\ClientScope;
use Closure;

class EnableGlobalClientScopeForUsers
{
    //ClientScope as GlobalScope on user breaks some areas where C&B Admins switch to a client team.
    //basically anywhere that we need to query users outside of the current client.  In our own controllers,
    //we can just use User::withoutGlobalScopes(), but in some packages, it's easier to just check for route
    //and disable based on that.
    protected static $route_whitelist = [
        "impersonation/off",
    ];

    //Adds a global ClientScope to User after all of the auth stuff middleware is complete
    public function handle($request, Closure $next)
    {
        if (! in_array($request->route()->uri ?? "", self::$route_whitelist)) {
            User::addGlobalScope(new ClientScope());
        }

        return $next($request);
    }
}
