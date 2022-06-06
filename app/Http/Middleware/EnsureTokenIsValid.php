<?php

namespace App\Http\Middleware;

use App\Models\Clients\Client;
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
            $request->merge(['client_id' => $user->client_id]);
        } elseif ($user->isCapeAndBayUser()) {
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
            $request->merge(['client_id' => $client_id]); //Merge client_id into request.
        }

        return $next($request);
    }
}
