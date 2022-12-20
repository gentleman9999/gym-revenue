<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\EndUsers\Actions\CreateEndUser;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\Users\Models\User;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMember
{
    use AsAction;

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
            'profile_picture.uuid' => 'sometimes|required',
            'profile_picture.key' => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket' => 'sometimes|required',
            'gender' => 'sometimes|required',
            'date_of_birth' => 'sometimes|required',
            'opportunity' => 'sometimes|required',
            'owner_user_id' => 'sometimes|required|exists:users,id',
            'notes' => 'nullable|array',
        ];
    }

    public function handle(array $data): Member
    {
        $status = false;
        $end_user = CreateEndUser::run($data);

        $end_user_id = $end_user->id;
        $category_id = AgreementCategory::whereName('Membership')->first()->id;

        $agreement_data['client_id'] = $data['client_id'];
        $agreement_data['agreement_category_id'] = $category_id;
        $agreement_data['gr_location_id'] = $data['gr_location_id'];
        $agreement_data['created_by'] = $data['current_user_id'];
        $agreement_data['end_user_id'] = $end_user->id;
        $agreement_data['agreement_template_id'] = AgreementTemplate::first()->id;
        $agreement_data['active'] = true;

        CreateAgreement::run($agreement_data);

        return Member::find($end_user_id);
    }

    public function asController(ActionRequest $request)
    {
        $data = $request->validated();
        $data['current_user_id'] = $request->user()->id;

        $endUser = $this->handle(
            $data
        );
    }
}
