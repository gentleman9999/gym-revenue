<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;

class RestoreEndUser extends BaseEndUserAction
{
    public function handle(EndUser $endUser)
    {
        EndUserAggregate::retrieve($endUser->id)->restore()->persist();

        return $endUser->refresh();
    }
}
