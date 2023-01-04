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

class TrashMemberGroup
{
    use AsAction;

    public function handle(string $member_group_id): bool
    {
        MemberGroupAggregate::retrieve($member_group_id)->trash()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('Member group.trash', MemberGroup::class);
    }

    public function asController(string $member_group_id): bool
    {
        return $this->handle($member_group_id);
    }

    public function htmlResponse(MemberGroup $member_group): RedirectResponse
    {
        Alert::success("Member group '{$member_group->name}' sent to trash")->flash();

        return Redirect::back();
    }
}
