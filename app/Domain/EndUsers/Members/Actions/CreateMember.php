<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\CreateEndUser;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateMember extends CreateEndUser
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

        return $current_user->can('members.create', Member::class);
    }

    public function asController(ActionRequest $request)
    {
        $data = $request->validated();
        $member = $this->handle(
            $data,
            request()->user(),
        );

        if ($request->user()) {
            ($this->getAggregate())::retrieve($member->id)->claim($request->user()->id)->persist();
        }

        return $member->refresh();
    }

    public function htmlResponse(Member $member): RedirectResponse
    {
        Alert::success("Member '{$member->name}' was created")->flash();

        return Redirect::route('data.members.edit', $member->id);
    }
}
