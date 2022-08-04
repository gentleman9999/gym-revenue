<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\UpdateEndUser;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateMember extends UpdateEndUser
{
    protected static function getModel(): EndUser
    {
        return new Member();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new MemberAggregate();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('members.update', Member::class);
    }

    public function asController(ActionRequest $request, Member $member)
    {
        $data = $request->validated();
        $member = $this->handle(
            $member,
            $data,
        );

        if ($request->user()) {
            AssignMemberToRep::run($member, $request->user());
        }

        return $member->refresh();
    }

    public function htmlResponse(Member $member): RedirectResponse
    {
        Alert::success("Member '{$member->name}' was updated")->flash();

        return Redirect::route('data.members.edit', $member->id);
    }
}
