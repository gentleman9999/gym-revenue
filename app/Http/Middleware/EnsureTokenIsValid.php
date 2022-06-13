<?php

namespace App\Http\Middleware;

use App\Domain\Clients\Models\Client;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
{
    private string $client_header = 'GR-Client';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['error' => 'You must provide a Bearer Token.'], 401);
        }
        //TODO: cache this
        $user = User::whereAccessToken($token)
            ->first();
        if (! $user) {
            return response()->json(['error' => 'Invalid Bearer Token.'], 403);
        }

        if ($user->isClientUser()) {
            $this->addClientIdToRequest($request, $user->client_id);
        } elseif ($user->isAdmin()) {
            $client_id = $request->header($this->client_header);
            if (! $client_id) {
                return response()->json(['error' => "You must provide a Client Id via the '{$this->client_header}' header"], 400);
            }
            //TODO: cache this
            $client = Client::whereId($client_id)
                ->first();
            if (! $client) {
                return response()->json(['error' => "Incorrect Client Id specified through '{$this->client_header}'"], 400);
            }
            $this->addClientIdToRequest($request, $client_id);
        }

        return $next($request);
    }

    protected function addClientIdToRequest($request, $client_id)
    {
        $body = $request->all();

        if (count($body) == count($body, COUNT_RECURSIVE)) {
            $request->merge(['client_id' => $client_id]);

            return $request;
        }

        foreach ($body as $idx => $object) {
            $object['client_id'] = $client_id;
            $body[$idx] = $object;
        }

        $request->merge($body);

        return $request;
    }
}
