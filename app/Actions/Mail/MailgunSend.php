<?php

declare(strict_types=1);

namespace App\Actions\Mail;

use App\Domain\Templates\Services\TemplateParserService;
use App\Domain\Users\Models\User;
use App\Models\Utility\AppState;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Lorisleiva\Actions\Action;
use Lorisleiva\Actions\Concerns\AsAction;
use Mailgun\Model\Message\SendResponse;

class MailgunSend extends Action
{
    use AsAction;

    public string $commandSignature = 'email-test {email}';

    /**
     * Execute the action and return a result.
     *
     * @param User[] $recipients
     *
     */
    public function handle(array $recipients, string $subject, string $markup): SendResponse
    {
        if (Arr::isAssoc($recipients)) {
            throw new InvalidArgumentException('recipients should not be an associate array.');
        }

        $list    = [];
        $parser  = new TemplateParserService($markup);
        $mailgun = $recipients[0]->client->getMailGunService();
        $is_dev  = AppState::isSimuationMode();

        // The maximum number of recipients allowed for Batch Sending is 1,000.
        foreach (array_chunk($recipients, 1000) as $users) {
            /** @var User $user */
            foreach ($users as $user) {
                // @todo this is not efficient, we need mailhog
                if ($is_dev) {
                    $user->email = env('TEST_EMAIL') ?: throw new InvalidArgumentException('"TEST_EMAIL" is required');
                }

                // Data structure:
                //  $list['foo@bar'] = ['user.first_name' => 'foo', 'user.last_name' => 'bar', ...]
                // Data access:
                // "%recipient.user.first_name%
                $list[$user->email] = $parser->getReplacedToken([
                    'user' => $user,
                    // Register data replacements here...
                ]);
            }

            // mail gun delimiter is '%'
            $markup     = preg_replace('~{{\s*(.*?)\s*}}~', '%recipient.$1%', $markup);
            $parameters = [
                'to' => $recipients,
                'subject' => $subject,
                // mailgun delimiter is '%'
                // https://documentation.mailgun.com/en/latest/user_manual.html#message-queue
                'html' => preg_replace('~{{\s*(.*?)\s*}}~', '%recipient.$1%', $markup),
                'recipient-variables' => json_encode($list),
            ];

            $mailgun->send($parameters);
        }

        return true;
    }

    public function asCommand(Command $command): void
    {
        $user = User::whereEmail($command->argument('email'))->firstOrFail();

        $this->handle([$user], 'Test', '{{user.first_name}} {{ user.last_name}} {{test.blank}}');
    }
}
