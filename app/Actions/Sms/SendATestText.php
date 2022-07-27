<?php

namespace App\Actions\Sms;

use App\Aggregates\Users\UserAggregate;
use App\Domain\Clients\Projections\ClientDetail;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class SendATestText
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
            'templateId' => 'bail|required|exists:sms_templates,id',
        ];
    }

    public function handle()
    {
        $results = false;

        $data = request()->all();
        $user = request()->user();
        $phone_detail = $user->phone_number;

        // Get the user and check if there is a phone number or fail with string
        if (! is_null($phone_detail)) {
            // Get the sms template from sms templates or fail with string
            $sms_template_record = SmsTemplate::find($data['templateId']);

            if (! is_null($sms_template_record)) {
                $session_team = session()->get('current_team');
                if ($session_team && array_key_exists('id', $session_team)) {
                    $current_team_id = $session_team['id'];
                } else {
                    $current_team_id = $user->default_team_id;
                }

                $client_id = null;
                if (! is_null($current_team_id)) {
                    $client_team_detail = ClientDetail::whereDetail('team')
                        ->whereValue($current_team_id)->first();
                    $client_id = $client_team_detail->client_id ?? null;
                }

                // Verify the sms going with the client of the active team is the same or its a gymrevenue template
                if ($client_id == $sms_template_record->client_id) {
//                    $user_aggy = UserAggregate::retrieve($user->id);
                    //TODO: fire off SMS with the given template to the User
                    $results = true;
                } else {
                    $results = 'This template does not belong to this Account.';
                }
            } else {
                $results = 'Invalid or decommissioned template.';
            }
        } else {
            $results = 'Missing Phone on Profile. Add it to use this feature.';
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
