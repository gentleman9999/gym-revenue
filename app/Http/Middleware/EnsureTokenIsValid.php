<?php

namespace App\Http\Middleware;

use App\Models\Clients\Client;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnsureTokenIsValid
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
        $header = $request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            $token = Str::substr($header, 7); // Splice token out of header
            $user = User::whereAccessToken($token)
                ->firstOrFail(); //Find user, if it fails, it will 404.

            if ($user->isClientUser()) {
                $request->merge(['client_id' => $user->client_id]);
            } elseif ($user->isCapeAndBayUser()) {
                $client_id = $request->header('Client');
                Client::whereId($client_id)
                    ->firstOrfail(); //Find client, if it fails, it will 404.
                $request->merge(['client_id' => $client_id]); //Merge client_id into request.
            }
        } else {
            abort(403);
        }

        return $next($request);
    }
}
