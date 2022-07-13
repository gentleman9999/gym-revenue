<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteLogo
{
    use AsAction;

    public function handle($data)
    {
        $logo = File::whereClientId($data['client_id'])->whereType('logo')->first();

        $logo->forceDelete();

        return Client::findOrFail($data['client_id']);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->all()
        );
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Logo Deleted.")->flash();


        return Redirect::route('settings');
    }
}
