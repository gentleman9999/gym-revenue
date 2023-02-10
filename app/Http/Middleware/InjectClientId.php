<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectClientId
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        if ($user->isClientUser()) {
            $this->addClientIdToRequest($request, $user->client_id);
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
            return true;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
