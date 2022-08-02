<?php

namespace App\Domain\LeadStatuses\Actions;

use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\LeadStatuses\LeadStatusAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateLeadStatus
{
    use AsAction;

    public function handle(string $id, array $data): LeadStatus
    {
        LeadStatusAggregate::retrieve($id)->update($data)->persist();

        return LeadStatus::findOrFail($id);
    }
}
