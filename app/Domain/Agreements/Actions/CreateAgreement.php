<?php

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\Contracts\ContractGates\Projections\ContractGate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreement
{
    use AsAction;

    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'agreement_category_id' => ['required', 'exists:agreement_categories,id'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'billing_schedule_id' => ['required', 'exists:billing_schedules,id'],
            'created_by' => 'required',
            'user_id' => ['required','exists:users,id'],
            'agreement_template_id' => ['required', 'exists:agreement_templates,id'],
            'agreement_json' => ['sometimes', 'json'],
            'contract_id' => ['required', 'exists:contracts,id'],
        ];
    }

    public function handle(array $data): Agreement
    {
        //Validating agreement through contract gate
        $entity_ids = [
            $data['agreement_category_id'],
            $data['gr_location_id'],
            $data['billing_schedule_id'],
        ];

        if (! ContractGate::validateContract($data['contract_id'], $entity_ids)) {
            return throw new \Exception("Agreement Category, Gym Revenue Location or Billing Schedule Type is not valid for this contract");
        }

        unset($data['billing_schedule_id']); // Don't have column in agreement table

        $id = Uuid::new();
        AgreementAggregate::retrieve($id)->create($data)->persist();

        return Agreement::findOrFail($id);
    }
}
