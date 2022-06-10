<?php

namespace App\Actions\Users;

use App\Aggregates\Users\UserAggregate;
use App\Models\Clients\Location;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class GrantAccessToken
{
    use AsAction;

    public string $commandSignature = 'access_token:create {user_id}';

    public function handle($current_user = null)
    {
        UserAggregate::retrieve($current_user->id)
            ->grantAccessToken()
            ->persist();

        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('access_token.create', Location::class);
    }

    public function asController(ActionRequest $request)
    {
        $this->handle(
            $request->user(),
        );


        Alert::success("Access Token Generated & granted.")->flash();


        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $this->handle(
            User::findOrFail($command->argument('user_id'))
        );


        $command->info('Done!');
    }
}
