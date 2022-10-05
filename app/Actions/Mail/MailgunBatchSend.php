<?php

declare(strict_types=1);

namespace App\Actions\Mail;

use App\Domain\Users\Models\User;
use App\Models\Utility\AppState;
use App\Services\TemplateParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use InvalidArgumentException;
use Lorisleiva\Actions\Action;
use Lorisleiva\Actions\Concerns\AsAction;
use Mailgun\Mailgun;

class MailgunBatchSend extends Action
{
    use AsAction;
    public string $commandSignature = 'batch-email-test {email*}';

    /**
     * @param array  $recipients list of emails.
     * @param string $subject
     * @param string $markup
     *
     * @return void
     */
    public function handle(array $recipients, string $subject, string $markup): void
    {
        if (Arr::isAssoc($recipients)) {
            throw new InvalidArgumentException('recipients should not be an associate array.');
        }

        if (AppState::isSimuationMode()) {
            $recipients = [Env::get('TEST_EMAIL')];
        }

        $list = [];
        $parser = new TemplateParserService($markup);
        $users = User::whereIn('email', $recipients)->get()->groupBy('email');
        $mailgun = Mailgun::create(Env::get('MAILGUN_SECRET'));
        $domain = Env::get('MAILGUN_DOMAIN');
        $from = Env::get('MAIL_FROM_ADDRESS');

        // The maximum number of recipients allowed for Batch Sending is 1,000.
        foreach (array_chunk($recipients, 1000) as $emails) {
            foreach ($emails as $email) {
                $list[$email] = $parser->getReplacedToken([
                    'user' => $users[$email][0], // access with "%recipient.user.first_name%
                    // Register data replacements here...
                ]);
            }

            // mail gun delimiter is '%'
            $markup = preg_replace('~{{\s*(.*?)\s*}}~', '%recipient.$1%', $markup);
            $parameters = [
                'from' => $from,
                'to' => $recipients,
                'subject' => $subject,
                // mailgun delimiter is '%'
                // https://documentation.mailgun.com/en/latest/user_manual.html#message-queue
                'html' => preg_replace('~{{\s*(.*?)\s*}}~', '%recipient.$1%', $markup),
                'recipient-variables' => json_encode($list),
            ];

            $mailgun->messages()->send($domain, $parameters);
        }
    }

    public function asCommand(Command $command)
    {
        $this->handle($command->argument('email'), 'Test', '{{user.first_name}} {{ user.last_name}} {{test.blank}}');
    }
}
