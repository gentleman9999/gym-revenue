<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use App\Support\Uuid;
use Illuminate\Console\Command;
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

    public function handle($data, $user)
    {
        $id = Uuid::new();
        $data['id'] = $id;

        UserAggregate::retrieve($user->id)
            ->createNotification($data)
            ->persist();

//        return Notification::findOrFail($id);
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            [
            'text' => $command->argument('text'),
        ],
            User::findOrFail($command->argument('user_id'))
        );
        $command->info('Notification Created');
    }
}
