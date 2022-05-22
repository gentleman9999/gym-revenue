<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Endusers\Member;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreMember
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
        ];
    }

    public function handle($id, $user = null)
    {
        $client_id = Member::withTrashed()->findOrFail($id)->client->id;
        EndUserActivityAggregate::retrieve($client_id)->restoreMember($user->id ?? "Auto Generated", $id)->persist();

        return Member::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('members.restore', Member::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $member = $this->handle(
            $id
        );

        Alert::success("Member '{$member->name}' restored.")->flash();

        return Redirect::back();
    }
}
