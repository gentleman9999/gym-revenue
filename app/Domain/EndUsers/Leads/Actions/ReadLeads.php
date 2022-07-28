<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\ReadEndUsers;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use Lorisleiva\Actions\ActionRequest;

class ReadLeads extends ReadEndUsers
{
    protected function getRelationshipsToLoad(): array
    {
        $base_relationships = parent::getRelationshipsToLoad();

        return array_merge($base_relationships, [
            'leadType',
            'leadSource',
        ]);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }

    protected static function getModel(): EndUser
    {
        return new Lead();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new LeadAggregate();
    }
}
