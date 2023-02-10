<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param mixed $request
     *
     */
    protected function redirectTo($request): null|string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
