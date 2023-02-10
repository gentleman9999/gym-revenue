<?php

declare(strict_types=1);

namespace App\Domain\Contracts\ContractGates\Actions;

use App\Domain\Contracts\ContractGates\ContractGateAggregate;
use App\Domain\Contracts\ContractGates\Projections\ContractGate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateContractGate
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'contract_id' => ['required', 'exists:contracts,id'],
            'client_id' => ['sometime', 'string'],
            'entity_id' => ['required', 'string'],
            'entity_type' => ['required', 'string'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): ContractGate
    {
        $id = Uuid::get();
        ContractGateAggregate::retrieve($id)->create($data)->persist();

        return ContractGate::findOrFail($id);
    }
}
