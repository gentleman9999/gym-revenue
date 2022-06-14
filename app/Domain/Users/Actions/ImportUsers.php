<?php

namespace App\Domain\Users\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class ImportUsers
{
    use AsAction;

    public function handle(array $data, string $client_id)
    {
        $result = false;
        foreach ($data as $item) {
            if ($item['extension'] === 'csv') {
                ClientAggregate::retrieve($client_id)->importUsers($item['key'])->persist();
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
            $request->user()->currentClientId()
        );
    }

    public function htmlResponse(bool $success): RedirectResponse
    {
        Alert::success("Users successfully imported")->flash();

        return Redirect::route('users');
    }
}
