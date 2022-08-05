<?php

namespace App\Domain\Email\Actions;

use App\Actions\Mail\MailgunBatchSend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class FireTestEmailMessage implements CreatesTeams
{
    use AsAction;

    public function handle(): bool
    {
        MailgunBatchSend::run(['adam@capeandbay.com', 'blair@capeandbay.com'], 'test', 'test');

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
