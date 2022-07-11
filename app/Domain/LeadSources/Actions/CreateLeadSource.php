<?php

namespace App\Domain\LeadSources\Actions;

use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadSources\LeadSourceAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLeadSource
{
    use AsAction;

    public function handle(array $data)
    {
        $id = Uuid::new();//we should use uuid here
        LeadSourceAggregate::retrieve($id)->create($data)->persist();

        return LeadSource::findOrFail($id);
    }
}
