<?php

namespace App\Domain\LeadTypes\Actions;

use App\Domain\LeadTypes\LeadType;
use App\Domain\LeadTypes\LeadTypeAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLeadType
{
    use AsAction;

    public function handle(array $data): LeadType
    {
        $id = Uuid::new();//we should use uuid here
        LeadTypeAggregate::retrieve($id)->create($data)->persist();

        return LeadType::findOrFail($id);
    }
}
