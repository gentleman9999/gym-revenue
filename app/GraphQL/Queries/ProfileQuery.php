<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Domain\Users\Models\User;

final class ProfileQuery
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $_args
     *
     * @return array<string, User>
     */
    public function __invoke($_, array $_args): array
    {
        // TODO implement the resolver
        $user = auth()->user();

        return [
            'user' => $user,
        ];
    }
}
