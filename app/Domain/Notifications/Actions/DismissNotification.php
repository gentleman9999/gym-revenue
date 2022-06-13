<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use App\Domain\Users\UserAggregate;
use App\Models\User;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

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
//            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($id, $user)
    {
        UserAggregate::retrieve($user->id)->dismissNotification($id)->persist();

        return GetUnreadNotificationCount::run($user);
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        return true;
//    }

    public function asController(ActionRequest $request, $id)
    {
//        $data = $request->validated();
        return $this->handle(
            $id,
            $request->user()
        );
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
