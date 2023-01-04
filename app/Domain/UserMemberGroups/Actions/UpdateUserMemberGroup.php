<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups\Actions;

use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use App\Domain\UserMemberGroups\UserMemberGroupAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateUserMemberGroup
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'client_id' => ['string', 'required'],
            'member_group_id' => ['string', 'required'],
            'user_id' => ['int', 'required'],
        ];
    }

    public function handle(UserMemberGroup $user_member_group, array $data): UserMemberGroup
    {
        UserMemberGroupAggregate::retrieve($user_member_group->id)
            ->update($data)
            ->persist();

        return $user_member_group->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('UserMemberGroup.update', UserMemberGroup::class);
    }

    public function asController(ActionRequest $request, UserMemberGroup $user_member_group): UserMemberGroup
    {
        return $this->handle(
            $user_member_group,
            $request->validated()
        );
    }

    public function htmlResponse(UserMemberGroup $user_member_group): RedirectResponse
    {
        Alert::success("User member group '{$user_member_group->name}' was updated")->flash();

        return Redirect::back();
    }
}
