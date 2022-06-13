<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectClientId
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user->isClientUser()) {
            $this->addClientIdToRequest($request, $user->client_id);
        } elseif ($user->isAdmin()) {
            $client_id = $user->currentClientId();
            $this->addClientIdToRequest($request, $client_id);
        }

        return $next($request);
    }

    protected function addClientIdToRequest($request, $client_id)
    {
        $body = $request->all();
        $body['client_id'] = $client_id;
        $request->merge($body);
    }
}
