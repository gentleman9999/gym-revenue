<?php

declare(strict_types=1);

namespace App\Actions\Mail;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Lorisleiva\Actions\Action;
use Lorisleiva\Actions\Concerns\AsAction;
use Mailgun\Mailgun;
use Mailgun\Model\Message\SendResponse;

class MailgunSend extends Action
{
    use AsAction;
    public string $commandSignature = 'batch-email-test {email*}';

    /**
     * Execute the action and return a result.
     *
     * @param array  $recipients
     * @param string $subject
     * @param string $markup
     *
     * @return SendResponse
     */
    public function handle(array $recipients, string $subject, string $markup): SendResponse
    {
        info('Mailgun, I choose you! Use BatchSend~~~~~');
        $parameters = [
            'from' => Env::get('MAIL_FROM_ADDRESS'),
            'to' => $recipients,
            'subject' => $subject,
            'html' => $markup,
        ];

        return Mailgun::create(Env::get('MAILGUN_SECRET'))->messages()->send(Env::get('MAILGUN_DOMAIN'), $parameters);
    }

    public function asCommand(Command $command)
    {
        $this->handle($command->argument('email'), 'Test', '{{user.first_name}} {{ user.last_name}} {{test.blank}}');
    }
}
