<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Endusers\Member;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteMember
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
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($client_id, $id, $user = null)
    {
        $member = Member::findOrFail($id);
        EndUserActivityAggregate::retrieve($client_id)->deleteMember($user->id ?? "Auto Generated", $id)->persist();

        return $member;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('members.update', Member::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $member = $this->handle(
            $data['client_id'],
            $id
        );

        Alert::success("Member'{$member->name}' was deleted")->flash();

        return Redirect::back();
    }
}
