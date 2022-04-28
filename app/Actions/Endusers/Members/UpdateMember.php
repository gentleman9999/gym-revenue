<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Endusers\Member;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;

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
            'first_name'                => ['required', 'max:50'],
            'middle_name'               => ['string', 'max:50', 'nullable'],
            'last_name'                 => ['required', 'max:30'],
            'email'                     => ['required', 'email:rfc,dns'],
            'primary_phone'             => ['required', 'string'],
            'alternate_phone'           => ['string', 'nullable'],
            'gr_location_id'            => ['required', 'exists:locations,gymrevenue_id'],
            'client_id'                 => ['required', 'exists:clients,id'],
            'profile_picture'           => ['array', 'nullable'],
            'profile_picture.uuid'      => 'sometimes|required|string',
            'profile_picture.key'       => 'sometimes|required|string',
            'profile_picture.extension' => 'sometimes|required|string',
            'profile_picture.bucket'    => 'sometimes|required|string',
            'gender'                    => 'string|required',
            'date_of_birth'             => 'required',
//            'agreement_number'          => ['required', 'string'],
            'notes'                     => 'nullable|array',
        ];
    }

    public function handle($data, $user=null)
    {
        EndUserActivityAggregate::retrieve($data['client_id'])
            ->updateMember($user->id ?? "Auto Generated" , $data)
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
        $member = $this->handle(
            $data
        );

        Alert::success("Member '{$member->name}' was updated")->flash();

        return Redirect::back();
    }

}
