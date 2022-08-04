<?php

namespace App\Domain\LeadSources\Actions;

use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadSources\LeadSourceAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateLeadSource
{
    use AsAction;

    public function handle(string $id, array $data): LeadSource
    {
        LeadSourceAggregate::retrieve($id)->update($data)->persist();

        return LeadSource::findOrFail($id);
    }
}
