<?php

declare(strict_types=1);

namespace App\Domain\LeadTypes\Actions;

use App\Domain\LeadTypes\LeadType;
use App\Domain\LeadTypes\LeadTypeAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLeadType
{
    use AsAction;

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): LeadType
    {
        $id = Uuid::get();//we should use uuid here
        LeadTypeAggregate::retrieve($id)->create($data)->persist();

        return LeadType::findOrFail($id);
    }
}
