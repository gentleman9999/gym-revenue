<?php

namespace App\GraphQL\Queries;

final class ProfileQuery
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        $user = auth()->user();

        return [
            'user' => $user,
        ];
    }
}
