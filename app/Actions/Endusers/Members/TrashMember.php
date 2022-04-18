<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Endusers\Member;
use Bouncer;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashMember
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
//            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle( $id, $user=null)
    {
        $member = Member::findOrFail($id);
        EndUserActivityAggregate::retrieve($member->client_id)->trashMember($user->id ?? "Auto Generated", $id)->persist();
        return $member;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('members.trash', Member::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $member = $this->handle(
            $id,
            $request->user()
        );

        Alert::success("Member '{$member->name}' sent to trash")->flash();

        return Redirect::back();
    }
}
