<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use App\Domain\Notifications\Validations\NotifiableInterface;
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
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['string', 'nullable'],
            'type' => ['required', 'string', 'nullable'],
            'color' => ['required', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle(array $data, User $user): void
    {
        // Default payloads if not specified.
        $data += [
            'id' => (string) Uuid::new(),
            'state' => Notification::STATE_DEFAULT,
            'type' => Notification::TYPE_DEFAULT,
        ];

        // This is a simple validation to assert that all required argument needed for an entity is specified.
        // Example. CalendarEvent requires that "entity" should have "start" and "title" property.
        if (isset($data['entity_type']) && (($entity = $data['entity_type']) instanceof NotifiableInterface)) {
            /** @var $entity NotifiableInterface */
            $entity->entityDataValidation($data);
        }

        UserAggregate::retrieve((string) $user->id)
            ->createNotification($data)
            ->persist();
    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $this->handle(
            ['text' => $command->argument('text')],
            User::findOrFail($command->argument('user_id'))
        );

        $command->info('Notification Created');
    }
}
