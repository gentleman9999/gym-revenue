<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\TrashEndUser;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class TrashMember extends TrashEndUser
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

        return $current_user->can('members.trash', Member::class);
    }

    public function asController(ActionRequest $request, Member $member)
    {
        return $this->handle(
            $member,
            $request->validated()['reason']
        );
    }

    public function htmlResponse(Member $member): RedirectResponse
    {
        Alert::success("Member '{$member->name}' sent to trash")->flash();

        return Redirect::back();
    }
}
