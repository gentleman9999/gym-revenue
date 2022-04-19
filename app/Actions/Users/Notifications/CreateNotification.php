<?php

namespace App\Actions\Users\Notifications;

use App\Aggregates\Users\UserAggregate;
use App\Helpers\Uuid;
use App\Models\CalendarEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateNotification
{
    use AsAction;

    public string $commandSignature = 'notifications:create {user_id} {text}';


    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['string', 'nullable'],
            'type' => ['required', 'string', 'nullable'],
            'color' => ['required', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($data, $user=null)
    {
        $id = Uuid::new();
        $data['id'] = $id;

        UserAggregate::retrieve($data['user_id'])
            ->createNotification($data)
            ->persist();

//        return Notification::findOrFail($id);
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        $current_user = $request->user();
//        return $current_user->can('calendar.create', CalendarEvent::class);
//    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle([
            'text' => $command->argument('text')
        ],
            User::findOrFail($command->argument('user_id'))
        );
        $command->info('Notification Created');
    }

}
