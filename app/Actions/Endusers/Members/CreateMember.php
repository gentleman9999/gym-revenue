<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\MemberAggregate;
use App\Helpers\Uuid;
use App\Models\Endusers\Member;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateMember
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'max:50'],
            'middle_name' => ['string', 'max:50', 'nullable'],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'primary_phone' => ['required', 'string'],
            'alternate_phone' => ['string', 'nullable'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'client_id' => ['required', 'exists:clients,id'],
            'profile_picture' => ['array', 'nullable'],
            'profile_picture.uuid' => 'sometimes|required|string',
            'profile_picture.key' => 'sometimes|required|string',
            'profile_picture.extension' => 'sometimes|required|string',
            'profile_picture.bucket' => 'sometimes|required|string',
            'gender' => 'string|required',
            'date_of_birth' => 'required',
//            'agreement_number'          => ['required', 'string'],
            'notes' => 'nullable|array',
        ];
    }

    public function handle($data, $user = null)
    {
        $id = Uuid::new();
        $data['id'] = $id;
        $data['location_id'] = $data['gr_location_id'];
        MemberAggregate::retrieve($data['client_id'])
            ->create($user->id ?? "Auto Generated", $data)
            ->persist();

        CheckIfMemberWasLead::run([
            'member_id' => $id,
            'email' => $data['email'],
        ]);

        return Member::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('members.create', Member::class);
    }

    public function asController(ActionRequest $request)
    {
        $member = $this->handle(
            $request->validated(),
            $request->user()
        );

        Alert::success("Member'{$member->name}' was created")->flash();

        return Redirect::route('data.members.edit', $member->id);
    }
}
