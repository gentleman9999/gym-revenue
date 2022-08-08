<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MailgunAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        foreach ($request->all() as $i => $k) {
            if ($i == "signature") {
                $token = $k['token'];
                $timestamp = $k['timestamp'];
                $signature = $k['signature'];
            }
        }

        $isValid = $this->verify(env('MAILGUN_SIGNING_KEY'), $token, $timestamp, $signature);

        if ($isValid) {
            return $next($request);
        } else {
            return response()->json(['error' => "You're not mailgun!"], 403);
        }
    }

    public function verify($signingKey, $token, $timestamp, $signature): bool
    {
        // check if the timestamp is fresh
        if (\abs(\time() - $timestamp) > 15) {
            return false;
        }

        // returns true if signature is valid
        return \hash_equals(\hash_hmac('sha256', $timestamp . $token, $signingKey), $signature);
    }
}
