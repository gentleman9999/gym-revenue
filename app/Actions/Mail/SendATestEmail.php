<?php

namespace App\Actions\Mail;

use App\Aggregates\Users\UserAggregate;
use App\Domain\Clients\Projections\Client;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Support\CurrentInfoRetriever;
use Lorisleiva\Actions\Concerns\AsAction;

class SendATestEmail
{
    use AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {
        // ...
    }

    public function rules(): array
    {
        return [
            'templateId' => 'bail|required|exists:email_templates,id',
        ];
    }

    public function handle()
    {
        $results = false;

        $data = request()->all();
        $user = request()->user();
        $email_detail = $user->email;

        // Get the user and check if there is an email or fail with string
        if (! is_null($email_detail)) {
            // Get the sms template from sms templates or fail with string
            $email_template_record = EmailTemplate::find($data['templateId']);

            if (! is_null($email_template_record)) {
                $current_team_id = CurrentInfoRetriever::getCurrentTeamID();

                $client_id = null;
                if (! is_null($current_team_id)) {
                    $client = Client::where('details->field', 'team')
                        ->where('value', $current_team_id)->first();

                    $client_id = $client->id ?? null;
                }

                // Verify the sms going with the client of the active team is the same or its a gymrevenue template
                if ($client_id == $email_template_record->client_id) {
//                    $user_aggy = UserAggregate::retrieve($user->id);
                    //TODO: fire off email with the given template to the user
                    $results = true;
                } else {
                    $results = 'This template does not belong to this Account.';
                }
            } else {
                $results = 'Invalid or decommissioned template.';
            }
        } else {
            $results = 'Missing Email on Profile. Add it to use this feature.';
        }

        return $results;
    }

    public function jsonResponse($result)
    {
        $results = ['success' => false, 'message' => 'Server Error. Message not sent'];
        $code = 500;

        if ($result) {
            if (is_string($result)) {
                $results['message'] = $result;
                $code = 401;
            } else {
                $results['success'] = true;
                $results['message'] = 'Success';
                $code = 200;
            }
        }

        return response($results, $code);
    }
}
