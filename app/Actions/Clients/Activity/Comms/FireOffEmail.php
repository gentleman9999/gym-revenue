<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Users\Models\User;
use App\Models\Comms\EmailTemplates;
use App\Models\Utility\AppState;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class FireOffEmail
{
    use AsAction;

//    public string $commandSignature = 'email:fire {templateId}';
//    public string $commandDescription = 'Fires off the emails for a given template id.';

    private int $batchSize = 100;//MAX IS 1000

    public function handle(string $client_id, $templateId, $entity_type, $entity_id)
    {
        $template = EmailTemplates::with('gateway')->findOrFail($templateId);
        $recipients = [];
        $sent_to = [];
        $client_aggy = ClientAggregate::retrieve($client_id);
        $entity = null;
        switch ($entity_type) {
            case 'user':
                $entity = User::find($entity_id);

                break;
            default:
                //todo:report error - unknown entity_Type
                break;
        }
        if ($entity) {
            $recipients[$entity->email] = ['email' => $entity->email, 'name' => $entity->name];
            $sent_to[] = [
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'email' => $entity->email,
            ];
        }

        $sent_to_chunks = array_chunk($sent_to, $this->batchSize);
        $idx = 0;
        foreach (array_chunk($recipients, $this->batchSize, true) as $chunk) {
            if (! AppState::isSimuationMode()) {
                $client_aggy->emailSent($template, $sent_to_chunks[$idx], Carbon::now())->persist();
            }
            $idx++;
        }
    }

    //TODO: fix asCommand. its broken.
    //command for ez development testing
//    public function asCommand(Command $command): void
//    {
//        $this->handle(
//            $command->argument('template_id')
//        );
//        if (AppState::isSimuationMode()) {
//            $command->info('Email skipped sending email because app is in simulation mode');
//        } else {
//            $command->info('Emails Sent!');
//        }
//    }
}
