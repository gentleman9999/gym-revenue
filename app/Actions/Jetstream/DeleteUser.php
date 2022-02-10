<?php

namespace App\Actions\Jetstream;

use App\Actions\Fortify\PasswordValidationRules;
use App\Aggregates\Clients\ClientAggregate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function dd;

class DeleteUser implements DeletesUsers
{
    use PasswordValidationRules, AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($id, $current_user)
    {
        $client_id = $current_user->currentClientId();

        if ($client_id) {
            ClientAggregate::retrieve($client_id)->deleteUser($current_user->id || "Auto Generated", ['id' => $id])->persist();
        } else {
            //CapeAndBay User
            dd('not yet implemented', $data);
        };
    }

    public function asController(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->handle(
            $id,
            $request->user(),
        );

        Alert::success("User '{$user->name}' was deleted")->flash();

//        return Redirect::route('users');
        return Redirect::back();
    }


    /**
     * Delete the given user.
     *
     * @param mixed $user
     * @return void
     */
    public function delete($user)
    {
        $this->run($user->id);
    }
}
