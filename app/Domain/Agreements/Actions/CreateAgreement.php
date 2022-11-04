<?php

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreement
{
    use AsAction;

    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'created_by' => 'required',
            'agreement_json' => 'required',
        ];
    }

    public function handle(array $data): Agreement
    {
        $id = Uuid::new();
        AgreementAggregate::retrieve($id)->create($data)->persist();


        return Agreement::findOrFail($id);
    }
}
