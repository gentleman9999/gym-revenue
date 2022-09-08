<?php

namespace App\Domain\Folders;

use App\Actions\Mail\MailgunBatchSend;
use App\Domain\Folders\Events\FolderSharingUpdated;
use App\Domain\Users\Models\User;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class FolderReactor extends Reactor
{
    public function onFolderSharingUpdated(FolderSharingUpdated $event)
    {
        if ($event->payload['user_ids']) {
            foreach ($event->payload['user_ids'] as $user_id) {
                $user = User::whereId($user_id)->first();
                $message = "<head>A folder has been shared with you, ".$user->name."</head>";
                $message .= "<br> ".ENV('APP_URL')."/folders/viewFiles/".$event->payload['id'];
                //MailgunBatchSend::run([$user->email], 'A folder has been shared with you!', $message);
                MailgunBatchSend::run('shivam@capeandbay.com', 'A folder has been shared with you!', $message);
            }
        }
    }
}
