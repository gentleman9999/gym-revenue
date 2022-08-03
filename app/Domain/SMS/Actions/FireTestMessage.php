<?php

namespace App\Domain\SMS\Actions;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class FireTestMessage implements CreatesTeams
{
    use AsAction;

    public function handle(): bool
    {
        FireTwilioMsg::run(env('TWILIO_TEST_NUMBER'), 'Test Message');

        return true;
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle();
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Message was created")->flash();

        return Redirect::route('comms.dashboard');
    }

    public function create($user, array $input)
    {
        return $this->handle($input);
    }
}
