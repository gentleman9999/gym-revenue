<?php

namespace App\Domain\Notes\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Note;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateNote
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
            'title' => ['string', 'required'],
            'id' => ['sometimes', 'nullable'],
            'entity_id' => ['string', 'sometimes', 'nullable'],
            'entity_type' => ['string', 'sometimes', 'nullable'],
            'note' => ['string', 'sometimes', 'nullable'],
            'created_by_user_id' => ['string', 'sometimes', 'nullable'],
            'active' => ['integer', 'sometimes', 'nullable'],
        ];
    }

    public function handle($data)
    {
        ClientAggregate::retrieve($data['created_by_user_id'])->updateNote($data['user_id'], $data)->persist();

        return Note::find($data['id']);
    }

    public function __invoke($_, array $args): Note
    {
        return $this->handle($args);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.update', Note::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $note = $this->handle(
            $data,
        );

        Alert::success("Note '{$note->title}' was updated")->flash();

        return Redirect::route('notes.edit', $id);
    }
}
