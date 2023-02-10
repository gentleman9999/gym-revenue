<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Actions;

use App\Domain\Contracts\ContractAggregate;
use App\Domain\Contracts\ContractGates\Actions\CreateContractGate;
use App\Domain\Contracts\Projections\Contract;
use App\Enums\ContractGateTypeEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateContract
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
            'name' => ['required', 'string', 'max:50'],
            'client_id' => ['string', 'required'],
            'gr_location_id' => [
                'required_without_all:agreement_category_id,billing_schedule_id',
                'exists:locations,gymrevenue_id',
            ],
            'agreement_category_id' => [
                'required_without_all:gr_location_id,billing_schedule_id',
                'exists:agreement_categories,id',
            ],
            'billing_schedule_id' => ['required_without_all:gr_location_id,agreement_category_id, exists:billing_schedules,id'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): Contract
    {
        $id = Uuid::get();
        ContractAggregate::retrieve($id)->create($data)->persist();

        return Contract::findOrFail($id);
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('contract.create', Contract::class);
    }

    public function asController(ActionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data     = $request->validated();
        $contract = $this->handle($data);

        //Decide entity id and type
        if (isset($data['gr_location_id'])) {
            $entity_id   = $data['gr_location_id'];
            $entity_type = ContractGateTypeEnum::Location;
        } elseif (isset($data['agreement_category_id'])) {
            $entity_id   = $data['agreement_category_id'];
            $entity_type = ContractGateTypeEnum::AgreementCategory;
        } elseif (isset($data['billing_schedule_id'])) {
            $entity_id   = $data['billing_schedule_id'];
            $entity_type = ContractGateTypeEnum::BillingSchedule;
        } else {
            $entity_id   = '';
            $entity_type = '';
        }

        if (! empty($entity_id) && ! empty($entity_type)) {
            $contract_gate_data                = [];
            $contract_gate_data['contract_id'] = $contract->id;
            $contract_gate_data['client_id']   = $contract->client_id;
            $contract_gate_data['entity_id']   = $entity_id;
            $contract_gate_data['entity_type'] = $entity_type;

            CreateContractGate::run($contract_gate_data);
        }

        Alert::success("Contract '{$contract->name}' was created")->flash();

        return Redirect::back();
    }
}
