<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\UserAggregate;
use App\Models\Clients\Location;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class GrantAccessToken
{
    use AsAction;

    public string $commandSignature = 'access_token:create {user_id}';

    public function handle(User $user): User
    {
        UserAggregate::retrieve($user->id)
            ->grantAccessToken()
            ->persist();

        return $user->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('access_token.create', Location::class);
    }

    public function asController(ActionRequest $request): User
    {
        return $this->handle(
            $request->user(),
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("Access Token Generated & granted.")->flash();

        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $this->handle(
            User::findOrFail($command->argument('user_id'))
        );


        $command->info('Created access token!');
    }
}
