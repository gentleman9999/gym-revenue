<?php

namespace App\Actions\Mail;

use Lorisleiva\Actions\Action;
use Mailgun\Mailgun;

class MailgunBatchSend extends Action
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
        info('Mailgun, I choose you! Use BatchSend~~~~~');

        $mg = Mailgun::create(env('MAILGUN_SECRET'));

        $parameters = [
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => $recipients,
            'subject' => $subject,
            'html' => $markup,
        ];
        $result = $mg->messages()->send('domain', $parameters);
    }
}
