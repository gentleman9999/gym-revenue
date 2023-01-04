<?php

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\Contracts\ContractGates\Projections\ContractGate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateAgreement
{
    use AsAction;

    public function handle(Agreement $audience, array $payload): Agreement
    {
        AgreementAggregate::retrieve($audience->id)->update($payload)->persist();

        return $audience->refresh();
    }

    public function rules(): array
    {
        return [
            'agreement_category_id' => ['required', 'exists:agreement_categories,id'],
            'created_by' => 'sometimes',
            'user_id' => ['sometimes','exists:users,id'],
            'agreement_template_id' => ['sometimes', 'exists:agreement_templates,id'],
            'gr_location_id' => ['sometimes', 'string', 'exists:locations,gymrevenue_id'],
            'agreement_json' => ['sometimes', 'json'],
            'billing_schedule_id' => ['sometimes', 'exists:billing_schedules,id'],
            'contract_id' => ['sometimes', 'exists:contracts,id'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, Agreement $agreement): Agreement
    {
        $data = $request->validated();

        if (isset($data['contract_id'])) {
            //Validating agreement through contract gate
            $entityIds = [$data['agreement_category_id']];

            if (isset($data['gr_location_id'])) {
                $entityIds[] = $data['gr_location_id'];
            }

            if (isset($data['billing_schedule_id'])) {
                $entityIds[] = $data['billing_schedule_id'];
            }

            if (! ContractGate::validateContract($data['contract_id'], $entityIds)) {
                return throw new \Exception("Agreement Category, Gym Revenue Location or Billing Schedule Type is not valid for this contract");
            }
        }

        unset($data['billing_schedule_id']); // Don't have column in agreement table

        return $this->handle(
            $agreement,
            $data
        );
    }

    public function htmlResponse(Agreement $agreement): RedirectResponse
    {
        Alert::success("Agreement '{$agreement->name}' was created")->flash();

        return Redirect::route('agreements.edit', $agreement->id);
    }
}
