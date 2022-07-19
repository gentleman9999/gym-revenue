<?php

namespace App\Domain\EndUsers\Leads\Reactors;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Reactors\EndUserCrudReactor;

class LeadCrudReactor extends EndUserCrudReactor
{
    public static function getModel(): EndUser
    {
        return new Lead();
    }

    public static function getAggregate(): EndUserAggregate
    {
        return new LeadAggregate();
    }
}
