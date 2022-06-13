<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\UserAggregate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class ImportUsers
{
    use AsAction;

    public function handle($data)
    {
        $result = false;
        foreach ($data as $item) {
            if ($item['extension'] === 'csv') {
                UserAggregate::retrieve($current_user->id)->importUsers($item['key'], $current_user->currentClientId())->persist();
                $result = true;
            } else {
                Alert::error("File name: ".$item['filename']. " doesn't meet extension requirements of '.csv'.")->flash();
            }
        }

        return $result;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.create', User::class);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->all(),
            $request->user(),
        );
    }

    public function htmlResponse(array $users): RedirectResponse
    {
        $num_users = count($users);
        Alert::success("{$num_users} users imported")->flash();

        return Redirect::route('users');
    }
}
