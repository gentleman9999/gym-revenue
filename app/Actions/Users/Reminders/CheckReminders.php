<?php

namespace App\Actions\Users\Reminders;

use App\Models\Calendar\CalendarEvent;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckReminders
{
    use AsAction;

    public string $commandSignature = 'reminders:check';
    public string $commandDescription = 'Fires off reminders that are ready.';

    public function handle()
    {
        Log::debug("checking for reminders");
        $reminders = Reminder::whereNull('triggered_at')->with('event')->get();

        $reminders->each(function($reminder){
            if($reminder->entity_type == CalendarEvent::class) {
                echo "reminder name: ".$reminder->name. "\n";
                echo "reminder event attached: ".$reminder->event->title. "\n";
                echo "reminder minutes: ".$reminder->remind_time. "\n";
                echo "calendar event date : ".$reminder->event->start. "\n";
                $time = date('Y-m-d H:i:s',strtotime($reminder->event->start. '-'.$reminder->remind_time.' minutes'));
                echo "time ".$time. "\n";
                if($time < date('Y-m-d H:i:s')){
                    echo " IT WORKED BOY\n";
                    TriggerReminder::run($reminder->id);
                }
            }
        });
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $command->info('Checking Reminders...');
        $this->handle();
        $command->info('Done Checking Reminders');
    }
}
