<?php

namespace App\Domain\SMS\Actions;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Domain\SMS\SmsAggregate;
use App\Domain\Users\Models\User;
use App\Models\GatewayProviders\GatewayProvider;
use App\Support\Uuid;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class FireTestMessage
{
    use AsAction;

    public function handle($user): bool
    {
        $client_id = $user->client_id;

        $test = FireTwilioMsg::run($user->phone, 'Test Message');

        $id = Uuid::new();
        $gateway = GatewayProvider::whereName('Twilio SMS')->first();
        $payload = [
            'id' => $id,
            'client_id' => $client_id,
            'message_id' => $test->sid,
            'recipient_type' => User::class,
            'recipient_id' => $user->id,
            'recipient_phone' => $user->phone,
            'gateway_id' => $gateway->id,
            'initiated_at' => Carbon::now(),
        ];
        SmsAggregate::retrieve($id)->smsLog($payload)->persist();

        return true;
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->user());
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Message was created")->flash();

        return Redirect::route('mass-comms.dashboard');
    }

    public function create($user, array $input)
    {
        return $this->handle($input);
    }
}
