<?php

declare(strict_types=1);

namespace App\Domain\EndUsers\Customers\Actions;

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\EndUsers\Actions\CreateEndUser;
use App\Domain\EndUsers\Customers\Projections\Customer;
use App\Domain\EndUsers\Projections\EndUser;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateCustomer
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'max:50'],
            'middle_name' => [],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'primary_phone' => ['required', 'string', 'min:10'],
            'alternate_phone' => ['sometimes', 'nullable', 'string', 'min:10'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'client_id' => 'required',
            'profile_picture' => 'sometimes',
            'profile_picture.uuid' => 'sometimes',
            'profile_picture.key' => 'sometimes',
            'profile_picture.extension' => 'sometimes',
            'profile_picture.bucket' => 'sometimes',
            'gender' => 'required',
            'date_of_birth' => 'required',
            // 'opportunity' => 'required',
            // 'owner_user_id' => 'required', 'exists:users,id',
            'notes' => 'nullable|array',
            'agreement_category_id' => ['required', 'exists:agreement_categories,id'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('customers.create', Customer::class);
    }

    public function handle(array $data): EndUser
    {
        $end_user = CreateEndUser::run($data);

        $agreement_data['client_id'] = $data['client_id'];
        $agreement_data['agreement_category_id'] = $data['agreement_category_id'];
        $agreement_data['gr_location_id'] = $data['gr_location_id'];
        $agreement_data['created_by'] = $data['current_user_id'];
        $agreement_data['end_user_id'] = $end_user->id;
        $agreement_data['agreement_template_id'] = AgreementTemplate::first()->id;
        $agreement_data['active'] = true;

        CreateAgreement::run($agreement_data);

        return $end_user;
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['current_user_id'] = $request->user()->id;
        $this->handle($data);

        Alert::success("Customer was created")->flash();

        return Redirect::back();
    }
}
