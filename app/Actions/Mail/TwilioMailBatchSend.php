<?php

namespace App\Actions\Mail;

use Lorisleiva\Actions\Action;
use SendGrid\Mail\Mail;

class TwilioMailBatchSend extends Action
{
    protected $getAttributesFromConstructor = ['recipients','markup'];

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
     * @param array $recipients
     * @param string $subject
     * @param string $markup
     * @return mixed
     */
    public function handle(array $recipients, string $subject, string $markup)
    {
        info('Sending Twilio Mail, batchsend enabled');

        $email = new Mail();
        $email->setFrom('test@example.com', );
        $email->setSubject($subject);
        $email->addTo($recipients);
        $email->addContent('text/html', $markup);
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        $response = $sendgrid->send($email);

        info('Twilio Mail Response', $response);
    }
}
