<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\AssignEndUserToRep;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use Lorisleiva\Actions\ActionRequest;

class AssignMemberToRep extends AssignEndUserToRep
{
    protected static function getModel(): EndUser
    {
        return new Member();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new MemberAggregate();
    }

    public function authorize(ActionRequest $request, Member $member): bool
    {
        if ($member->owner_user_id !== null) {
            return false;
        }

        return $this->authorizeViaBouncer($request);
    }

    public function asController(ActionRequest $request, Member $member)
    {
        return $this->handle(
            $member,
            $request->user(),
        );
    }

//    public function htmlResponse(Member $member): RedirectResponse
//    {
//        Alert::success("Lead '{$member->name}' assigned to Rep")->flash();
//
//        return Redirect::back();
//    }
}
