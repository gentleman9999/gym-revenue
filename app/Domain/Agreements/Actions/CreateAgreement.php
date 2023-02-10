<?php

declare(strict_types=1);

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Contracts\ContractGates\Projections\ContractGate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreement
{
    use AsAction;

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'agreement_category_id' => ['required', 'exists:agreement_categories,id'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'created_by' => 'required',
            'user_id' => ['required','exists:users,id'],
            'agreement_template_id' => ['required', 'exists:agreement_templates,id'],
            'agreement_json' => ['sometimes', 'json'],
            'contract_file_id' => ['sometimes', 'exists:files,id'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function handle(array $data): Agreement
    {
        //Getting contract id and billing schedule id from agreement template
        $agreement_template_data = AgreementTemplate::find($data['agreement_template_id']);

        //Validating agreement through contract gate
        $entity_ids = [
            $data['agreement_category_id'],
            $data['gr_location_id'],
            $agreement_template_data->billing_schedule_id,
        ];

        if (! ContractGate::validateContract($agreement_template_data->contract_id, $entity_ids)) {
            return throw new \Exception("Agreement Category, Gym Revenue Location or Billing Schedule Type is not valid for this contract");
        }

        $id = Uuid::get();
        AgreementAggregate::retrieve($id)->create($data)->persist();

        return Agreement::findOrFail($id);
    }
}
