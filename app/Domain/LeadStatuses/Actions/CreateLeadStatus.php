<?php

namespace App\Domain\LeadStatuses\Actions;

use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\LeadStatuses\LeadStatusAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLeadStatus
{
    use AsAction;

    public function handle(array $data)
    {
        $id = Uuid::new();//we should use uuid here
        LeadStatusAggregate::retrieve($id)->create($data)->persist();

        return LeadStatus::findOrFail($id);
    }
}
