<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Aggregates\ChatParticipantAggregate;
use App\Domain\Chat\Models\Chat;
use App\Domain\Chat\Models\ChatParticipant;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Notifications\Notification;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateParticipant
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // int[]
            'participant_ids' => ['required', 'array'],
        ];
    }

    /**
     *
     */
    public function handle(Chat $chat, User $user): ChatParticipant
    {
        $id = Uuid::get();
        ChatParticipantAggregate::retrieve($id)->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ])->persist();

        $data = [
            'chat_id' => $chat->id,
            'participant' => $participant = ChatParticipant::with(['user'])->where(['id' => $id])->firstOrFail(),
            'type' => Notification::TYPE_NEW_CHAT_PARTICIPANT,
        ];

        $chat->participants()->each(static fn (ChatParticipant $p) => CreateNotification::run($data, $p->user));

        return $participant;
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('chat.create', ChatParticipant::class);
    }

    public function asController(ActionRequest $request, Chat $chat): void
    {
        $participant_ids = $request->validated()['participant_ids'];
        foreach ($participant_ids as $user_id) {
            $this->handle($chat, User::find($user_id));
        }
    }
}
