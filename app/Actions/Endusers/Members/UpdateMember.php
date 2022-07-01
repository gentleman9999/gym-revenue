<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\MemberAggregate;
use App\Models\Endusers\Member;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateMember
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
            'first_name' => ['sometimes', 'required', 'max:50'],
            'middle_name' => ['sometimes', 'string', 'max:50', 'nullable'],
            'last_name' => ['sometimes', 'required', 'max:30'],
            'email' => ['sometimes', 'required', 'email:rfc,dns'],
            'primary_phone' => ['sometimes', 'required', 'string'],
            'alternate_phone' => ['sometimes', 'string', 'nullable'],
            'gr_location_id' => ['sometimes', 'required', 'exists:locations,gymrevenue_id'],
            'client_id' => ['sometimes', 'required', 'exists:clients,id'],
            'profile_picture' => ['sometimes', 'array', 'nullable'],
            'profile_picture.uuid' => 'sometimes|required|string',
            'profile_picture.key' => 'sometimes|required|string',
            'profile_picture.extension' => 'sometimes|required|string',
            'profile_picture.bucket' => 'sometimes|required|string',
            'gender' => 'sometimes|string|required',
            'date_of_birth' => 'sometimes|required',
//            'agreement_number'          => ['required', 'string'],
            'notes' => 'sometimes|nullable|array',
        ];
    }

    public function handle($data, $user = null)
    {
        MemberAggregate::retrieve($data['client_id'])
            ->update($user->id ?? "Auto Generated", $data)
            ->persist();

        return Member::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('members.update', Member::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['client_id'] = $request->user()->currentClientId();
        $member = $this->handle(
            $data
        );

        Alert::success("Member '{$member->name}' was updated")->flash();

//        return Redirect::back();
        return Redirect::route('data.members.edit', $member->id);
    }
}
