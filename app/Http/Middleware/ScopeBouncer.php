<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\Bouncer;

class ScopeBouncer
{
    /**
     * The Bouncer instance.
     *
     */
    protected \Silber\Bouncer\Bouncer $bouncer;

    /**
     * Constructor.
     *
     */
    public function __construct(Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): mixed
    {
        $user = $request->user();
        if ($user) {
            $clientId = $request->user()->client_id;
            $this->bouncer->scope()->to($clientId);
        }

        return $next($request);
    }
}
