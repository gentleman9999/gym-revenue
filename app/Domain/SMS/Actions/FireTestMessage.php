<?php

namespace App\Domain\SMS\Actions;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Domain\Teams\Models\Team;
use App\Http\Middleware\InjectClientId;
use App\Services\GatewayProviders\Profiles\SMS\Twilio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class FireTestMessage implements CreatesTeams
{
    use AsAction;

    public function handle(array $payload): Team
    {
        FireTwilioMsg::run('4239942372', 'this is a test');

        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],
            'name' => ['required', 'max:50'],
            'home_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }*/
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.create', Team::class);
    }

    public function asController(ActionRequest $request): Team
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(Twilio $sms): RedirectResponse
    {
        Alert::success("Message was created")->flash();

        return Redirect::route('comms');
    }

    public function create($user, array $input): Team
    {
        return $this->handle($input);
    }
}
