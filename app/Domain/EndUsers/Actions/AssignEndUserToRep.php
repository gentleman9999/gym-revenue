<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;

abstract class AssignEndUserToRep extends BaseEndUserAction
{
    public function handle(EndUser $endUser, User $user)
    {
        ($this->getAggregate())::retrieve($endUser->id)->claim($user->id)->persist();

        return $endUser->refresh();
    }
}
