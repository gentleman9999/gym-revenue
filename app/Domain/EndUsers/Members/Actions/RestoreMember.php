<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\RestoreEndUser;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class RestoreMember extends RestoreEndUser
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

        return $current_user->can('leads.restore', Lead::class);
    }

    public function asController(Request $request, Member $member)
    {
        return $this->handle($member);
    }

    public function htmlResponse(Member $member): RedirectResponse
    {
        Alert::success("Member '{$member->name}' restored.")->flash();
//        return Redirect::route('data.leads');
        return Redirect::back();
    }
}
