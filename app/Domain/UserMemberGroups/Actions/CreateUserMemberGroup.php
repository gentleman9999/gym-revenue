<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups\Actions;

use App\Domain\UserMemberGroups\UserMemberGroupAggregate;
use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateUserMemberGroup
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
            'user_id' => ['int', 'required']
        ];
    }

    public function handle(array $data): UserMemberGroup
    {
        $id = Uuid::get();
        UserMemberGroupAggregate::retrieve($id)->create($data)->persist();

        return UserMemberGroup::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('UserMemberGroup.create', UserMemberGroup::class);
    }

    public function asController(ActionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user_member_group = $this->handle($request->validated());

        Alert::success("User member group '{$user_member_group->name}' was created")->flash();

        return Redirect::back();
    }
}
