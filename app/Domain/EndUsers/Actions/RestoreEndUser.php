<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;

abstract class RestoreEndUser extends BaseEndUserAction
{
    public function handle(EndUser $endUser)
    {
        ($this->getAggregate())::retrieve($endUser->id)->restore()->persist();

        return $endUser->refresh();
    }
}
