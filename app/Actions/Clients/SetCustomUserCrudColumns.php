<?php

namespace App\Actions\Clients;

use App\Aggregates\Users\UserAggregate;
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
            'table' =>['required', 'string','max:50'],
            'columns' => ['required', 'array', 'min:1', 'max:7']
        ];
    }

    public function handle($data, $user = null)
    {
        UserAggregate::retrieve($user->id)->setCustomCrudColumns($data['table'], $data['columns'])->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
//        $current_user = $request->user();
        return true;
    }

    public function asController(ActionRequest $request)
    {

        $data =  $request->validated();

        $this->handle(
           $data,
            $request->user()
        );

        Alert::success("CRUD columns updated for '{$data['table']}'")->flash();

        return Redirect::back();
    }

}
