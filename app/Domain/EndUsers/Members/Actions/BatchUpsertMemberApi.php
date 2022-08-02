<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\BatchUpsertEndUserApi;
use App\Domain\EndUsers\Actions\UpsertEndUserApi;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;

class BatchUpsertMemberApi extends BatchUpsertEndUserApi
{
    protected static function getModel(): EndUser
    {
        return new Member();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new MemberAggregate();
    }

    protected function getUpsertAction(): UpsertEndUserApi
    {
        return new UpsertMemberApi();
    }
}
