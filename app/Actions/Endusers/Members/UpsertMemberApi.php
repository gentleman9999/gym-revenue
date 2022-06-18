<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\MemberAggregate;
use App\Helpers\Uuid;
use App\Models\Endusers\Member;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpsertMemberApi
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
            'notes' => 'nullable|array',
            'external_id' => ['sometimes', 'nullable'],
        ];
    }

    public function handle($data, $user = null)
    {
        $id = Uuid::new();
        $data['id'] = $id;

        if (array_key_exists('external_id', $data)) {
            $member = Member::whereEmail($data['email'])
                ->orWhere('external_id', $data['external_id'])
                ->first();
        } else {
            $member = Member::whereEmail($data['email'])
                ->first();
        }
        if (is_null($member)) { //if no records of the member exist, create one
            MemberAggregate::retrieve($data['client_id'])
                ->create($user->id ?? "Auto Generated", $data)
                ->persist();
        } else {
            $id = $data['id'] = $member->id;
            MemberAggregate::retrieve($data['client_id'])
                    ->update($user->id ?? "Auto Generated", $data)
                    ->persist();
        }

        CheckIfMemberWasLead::run([
            'member_id' => $id,
            'email' => $data['email'],
        ]);


        return Member::findOrFail($id);
    }

    public function asController(ActionRequest $request)
    {
        $member = $this->handle(
            $request->validated(),
        );

        return $member;
    }
}
