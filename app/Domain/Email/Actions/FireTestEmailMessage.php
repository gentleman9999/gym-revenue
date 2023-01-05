<?php

declare(strict_types=1);

namespace App\Domain\Email\Actions;

use App\Actions\Mail\MailgunSend;
use App\Domain\Email\EmailAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Users\Models\User;
use App\Models\GatewayProviders\GatewayProvider;
use App\Support\Uuid;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class FireTestEmailMessage implements CreatesTeams
{
    use AsAction;

    public function handle(string $template_id, User $user): bool
    {
        $message = EmailTemplate::find($template_id)->parseMarkup(['user' => $user]);
        $mail = MailgunSend::run([$user], 'Test Message', $message);
        $id = (string) Uuid::new();
        $gateway = GatewayProvider::whereName('Mailgun')->first();

        $payload = [
            'id' => $id,
            'client_id' => $user->client_id,
            'message_id' => substr($mail->getId(), 1, -1),
            'recipient_type' => User::class,
            'recipient_id' => $user->id,
            'recipient_email' => $user->email,
            'gateway_id' => $gateway->id,
            'initiated_at' => Carbon::now(),
        ];

        EmailAggregate::retrieve($id)->emailLog($payload)->persist();

        return true;
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->templateId, $request->user());
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
