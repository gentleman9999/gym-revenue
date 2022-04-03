<?php

namespace App\Actions\Fortify;


use App\Aggregates\Clients\NoteAggregate;
use App\Models\ReadReceipt;
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
            'client_id' =>['string', 'sometimes'],
            'all_notes' => ['array', 'nullable'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        foreach($data['all_notes'] as $item)
        {
            NoteAggregate::retrieve($current_user->currentClientId())
                ->createReadReciept($current_user['id'], [
                'note_id' => $item['id'],
                'read_by_user_id' => $current_user['id'],
            ])
                ->persist();
        }
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
