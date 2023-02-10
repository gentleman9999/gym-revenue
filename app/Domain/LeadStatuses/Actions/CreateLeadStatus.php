<?php

declare(strict_types=1);

namespace App\Domain\LeadStatuses\Actions;

use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\LeadStatuses\LeadStatusAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLeadStatus
{
    use AsAction;

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): LeadStatus
    {
        $id = Uuid::get();//we should use uuid here
        LeadStatusAggregate::retrieve($id)->create($data)->persist();

        return LeadStatus::findOrFail($id);
    }
}
