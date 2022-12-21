<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups\Actions;

use App\Domain\MemberGroups\MemberGroupAggregate;
use App\Domain\MemberGroups\Projections\MemberGroup;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use App\Enums\MemberGroupTypeEnum;

class CreateMemberGroup
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
            'type' => ['required',  new Enum(MemberGroupTypeEnum::class)],
            'poc_name' => ['sometimes', 'string'],
            'poc_phone' => ['sometimes', 'string'],
            'poc_email' => ['sometimes', 'string']
        ];
    }

    public function handle(array $data): MemberGroup
    {
        $id = Uuid::get();
        MemberGroupAggregate::retrieve($id)->create($data)->persist();

        return MemberGroup::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('Member group.create', MemberGroup::class);
    }

    public function asController(ActionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $member_group = $this->handle($request->validated());

        Alert::success("Member group '{$member_group->name}' was created")->flash();

        return Redirect::back();
    }
}
