<?php

declare(strict_types=1);

namespace App\Actions\Mail;

use Lorisleiva\Actions\Action;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

class TwilioMailBatchSend extends Action
{
    protected array $getAttributesFromConstructor = ['recipients', 'markup'];

    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Execute the action and return a result.
     *
     * @param array<string> $recipients
     * @param string        $subject
     * @param string        $markup
     *
     * @return void
     * @throws TypeException
     */
    public function handle(array $recipients, string $subject, string $markup): void
    {
        info('Sending Twilio Mail, batchsend enabled');

        $email = new Mail();
        $email->setFrom('test@example.com',);
        $email->setSubject($subject);
        $email->addTo($recipients);
        $email->addContent('text/html', $markup);
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        $response = $sendgrid->send($email);

        info('Twilio Mail Response', $response);
    }
}
