<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups\Actions;

use App\Domain\MemberGroups\MemberGroupAggregate;
use App\Domain\MemberGroups\Projections\MemberGroup;
use App\Enums\MemberGroupTypeEnum;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateMemberGroup
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
            'client_id' => ['required', 'string'],
            'type' => ['required', new Enum(MemberGroupTypeEnum::class)],
            'poc_name' => ['sometimes', 'string'],
            'poc_phone' => ['sometimes', 'string'],
            'poc_email' => ['sometimes', 'string'],
        ];
    }

    public function handle(MemberGroup $member_group, array $data): MemberGroup
    {
        MemberGroupAggregate::retrieve($member_group->id)
            ->update($data)
            ->persist();

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

        return $current_user->can('Member group.update', MemberGroup::class);
    }

    public function asController(ActionRequest $request, MemberGroup $member_group): MemberGroup
    {
        return $this->handle(
            $member_group,
            $request->validated()
        );
    }

    public function htmlResponse(MemberGroup $member_group): RedirectResponse
    {
        Alert::success("Member group '{$member_group->name}' was updated")->flash();

        return Redirect::back();
    }
}
