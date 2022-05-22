<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Comms\SmsTemplates;
use App\Models\User;
use App\Models\Utility\AppState;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class FireOffSms
{
    use AsAction;

    public string $commandSignature = 'sms:fire {templateId}';
    public string $commandDescription = 'Fires off the SMS for a given template id.';

    protected $tokens = ['name'];

    public function handle(string $client_id, $templateId, $entity_type, $entity_id)
    {
        $template = SmsTemplates::with('gateway')->findOrFail($templateId);
        $client_aggy = ClientAggregate::retrieve($client_id);
        $sent_to = [];
        $member = null;
        switch ($entity_type) {
            case 'user':
                $member = User::with('phone')->find($entity_id);

                break;
            default:
                //todo:report error - unknown entity_Type
                break;
        }
        if ($member) {
            if ($member->phone) {
                //TODO: we need to scrutinize phone format here
                if (! AppState::isSimuationMode()) {
                    $sent_to[] = [
                        'entity_type' => $entity_type,
                        'entity_id' => $entity_id,
                        'phone' => $member->phone->value,
                    ];
                    $client_aggy->smsSent($template, $sent_to, Carbon::now())->persist();
                }
            }
        }
    }

    protected function transform($string, $data)
    {
        foreach ($this->tokens as $token) {
            $string = str_replace("%{$token}%", $data[$token] ?? 'UNKNOWN_TOKEN', $string);
        }

        return $string;
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            $command->argument('sms_campaign_id')
        );
        if (AppState::isSimuationMode()) {
            $command->info('SMS skipped because app is in simulation mode');
        } else {
            $command->info('SMS Sent!');
        }
    }
}
