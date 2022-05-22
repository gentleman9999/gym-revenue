<?php

namespace App\Actions\Fortify;

use App\Aggregates\Clients\NoteAggregate;
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
    public function rules()
    {
        return [
            'client_id' => ['string', 'sometimes'],
            'note' => ['array', 'nullable'],
        ];
    }

    public function handle($data, $current_user)
    {
        NoteAggregate::retrieve($current_user->currentClientId())
                ->createReadReciept($current_user->id, [
                'note_id' => $data['note']['id'],
                'read_by_user_id' => $current_user->id,
            ])
                ->persist();

        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        return true; //no auth required
    }

    public function asController(ActionRequest $request)
    {
        $note = $this->handle(
            $request->validated(),
            $request->user(),
        );
    }
}
