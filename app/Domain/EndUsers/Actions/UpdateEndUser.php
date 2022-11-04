<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateEndUser extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['sometimes', 'max:50'],
            'middle_name' => ['sometimes'],
            'last_name' => ['sometimes', 'max:30'],
            'email' => ['sometimes', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['sometimes', 'exists:locations,gymrevenue_id'],
            'profile_picture' => 'sometimes',
            'profile_picture.uuid' => 'sometimes|required',
            'profile_picture.key' => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket' => 'sometimes|required',
            'gender' => 'sometimes|required',
            'date_of_birth' => 'sometimes|required',
            'opportunity' => 'sometimes|required',
            'owner' => 'sometimes|required|exists:users,id',
            'notes' => 'nullable|array',
            'external_id' => ['sometimes', 'nullable'],
        ];
    }

    public function handle(EndUser $endUser, array $data)
    {
        EndUserAggregate::retrieve($endUser->id)->update($data)->persist();

        return $endUser->refresh();
    }

    public function asController(ActionRequest $request, EndUser $endUser): EndUser
    {
        $data = $request->validated();

        return $this->handle(
            $endUser,
            $data,
        );
    }

    public function htmlResponse(EndUser $endUser)
    {
        Alert::success("End User '{$endUser->first_name} {$endUser->last_name}' was updated")->flash();

        return Redirect::back();
    }
}
