<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups\Actions;

use App\Domain\UserMemberGroups\UserMemberGroupAggregate;
use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteUserMemberGroup
{
    use AsAction;

    public function handle(string $user_member_group_id): bool
    {
        UserMemberGroupAggregate::retrieve($user_member_group_id)->delete()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('UserMemberGroup.delete', UserMemberGroup::class);
    }

    public function asController(string $user_member_group_id): bool
    {
        return $this->handle($user_member_group_id);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("UserMemberGroup was deleted")->flash();

        return Redirect::back();
    }
}
