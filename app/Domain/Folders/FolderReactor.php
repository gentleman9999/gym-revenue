<?php

declare(strict_types=1);

namespace App\Domain\Folders;

use App\Actions\Mail\MailgunSend;
use App\Domain\Folders\Events\FolderSharingUpdated;
use App\Domain\Users\Models\User;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class FolderReactor extends Reactor
{
    public function onFolderSharingUpdated(FolderSharingUpdated $event)
    {
        if (isset($event->payload['user_ids'])) {
            $message = '<head>A folder has been shared with you, %recipient.user.name%</head><br />';
            $message .= ENV('APP_URL') . "/folders/viewFiles/{$event->payload['id']}";

            MailgunSend::run(
                User::whereIn('id', $event->payload['user_ids'])->all(),
                'A folder has been shared with you!',
                $message
            );
        }
    }
}
