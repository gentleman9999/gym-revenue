<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureTokenIsValid
{
    private string $client_header = 'GR-Client';

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
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

        if ($this->isStringKeyedArray($body)) {
            $request->merge(['client_id' => $client_id]);

            return $request;
        }
        //is an array of objects
        foreach ($body as $idx => $object) {
            $object['client_id'] = $client_id;
            $body[$idx]          = $object;
        }

        $request->merge($body);

        return $request;
    }

    protected function isStringKeyedArray(array $arr)
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
