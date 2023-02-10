<?php

declare(strict_types=1);

namespace App\Actions\Notes;

use App\Aggregates\Clients\NoteAggregate;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MarkNoteAsRead
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
            'client_id' => ['string', 'sometimes'],
            'note' => ['array', 'nullable'],
        ];
    }

    public function handle($data, $current_user)
    {
        NoteAggregate::retrieve($current_user->client_id)
                ->createReadReciept($current_user->id, [
                    'note_id' => $data['note']['id'],
                    'read_by_user_id' => $current_user->id,
                ])
                ->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        return true; //no auth required
    }

    public function asController(ActionRequest $request): void
    {
        $note = $this->handle(
            $request->validated(),
            $request->user(),
        );
    }
}
