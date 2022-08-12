<?php

namespace App\Actions\Sms\Twilio;

use Lorisleiva\Actions\Action;
use Twilio\Rest\Client as Twilio;

class FireTwilioMsg extends Action
{
    protected $getAttributesFromConstructor = ['phone_number','msg'];

    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     * @param string $msg
     * @param string $phone_number
     * @return mixed
     */
    public function handle(string $phone_number, string $msg)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $sms_client = new Twilio($sid, $token);
        $payload = ['from' => env('TWILIO_NO'), 'body' => $msg, 'statusCallBack' => env('TWILIO_STATUS_CALLBACK_DOMAIN').'/api/twilio/statusCallBack'];
        $message = $sms_client->messages->create($phone_number, $payload);
        info('Message - ', $message->toArray());
    }
}
