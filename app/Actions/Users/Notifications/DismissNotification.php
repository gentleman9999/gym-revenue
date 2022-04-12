<?php

namespace App\Actions\Users\Notifications;

use App\Aggregates\Clients\CalendarAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\CalendarEvent;
use App\Models\Clients\Location;
use App\Models\Notification;
use App\Models\User;
use Bouncer;
use App\Actions\Fortify\PasswordValidationRules;
use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Mockery\Matcher\Not;
use Prologue\Alerts\Facades\Alert;

class DismissNotification
{
    use AsAction;

    public string $commandSignature = 'notifications:dismiss {id}';


    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($id, $user)
    {
        UserAggregate::retrieve($user->id)->dismissNotification($id)->persist();
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        return true;
//    }

    public function asController(ActionRequest $request, $id)
    {
//        $data = $request->validated();
        $notification = $this->handle(
            $id,
            $request->user()
        );
//        return Redirect::back();
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $notification = Notification::findOrFail($command->argument('id'));
        $this->handle(
            $command->argument('id'),
            User::findOrFail($notification->user->id)
        );
        $command->info('Notification Dismissed');
    }
}
