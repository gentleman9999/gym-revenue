<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class SetCustomUserCrudColumns
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'table' => ['required', 'string','max:50'],
            'columns' => ['required', 'array', 'min:1', 'max:7'],
        ];
    }

    public function handle(array $data, User $user): User
    {
        UserAggregate::retrieve($user->id)->setCustomCrudColumns($data['table'], $data['columns'])->persist();

        return $user->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
//        $current_user = $request->user();
        return true;
    }

    public function asController(ActionRequest $request): User
    {
        $data = $request->validated();

        return $this->handle(
            $data,
            $request->user()
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("CRUD columns updated")->flash();

        return Redirect::back();
    }
}
