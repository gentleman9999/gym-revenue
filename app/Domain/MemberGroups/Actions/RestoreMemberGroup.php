<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups\Actions;

use App\Domain\MemberGroups\MemberGroupAggregate;
use App\Domain\MemberGroups\Projections\MemberGroup;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreMemberGroup
{
    use AsAction;

    public function handle(MemberGroup $member_group): MemberGroup
    {
        MemberGroupAggregate::retrieve($member_group->id)->restore()->persist();

        return $member_group->refresh();
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('Member group.restore', MemberGroup::class);
    }

    public function asController(MemberGroup $member_group): MemberGroup
    {
        return $this->handle($member_group);
    }

    public function htmlResponse(MemberGroup $member_group): RedirectResponse
    {
        Alert::success("Member group '{$member_group->name}' restored.")->flash();

        return Redirect::back();
    }
}
