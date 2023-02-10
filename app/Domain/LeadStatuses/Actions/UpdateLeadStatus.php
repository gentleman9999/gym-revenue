<?php

declare(strict_types=1);

namespace App\Domain\LeadStatuses\Actions;

use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\LeadStatuses\LeadStatusAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateLeadStatus
{
    use AsAction;

    /**
     * @param string $id
     * @param array<string, mixed>  $data
     *
     */
    public function handle(string $id, array $data): LeadStatus
    {
        LeadStatusAggregate::retrieve($id)->update($data)->persist();

        return LeadStatus::findOrFail($id);
    }
}
