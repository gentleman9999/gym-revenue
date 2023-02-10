<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Note;
use App\Support\Uuid;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateNote
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
            'title' => ['string', 'required'],
            'id' => ['integer', 'sometimes', 'nullable'],
            'entity_id' => ['string', 'sometimes', 'nullable'],
            'entity_type' => ['string', 'sometimes', 'nullable'],
            'note' => ['string', 'sometimes', 'nullable'],
            'created_by_user_id' => ['string', 'sometimes', 'nullable'],
            'active' => ['boolean', 'sometimes', 'nullable'],
            'created_at' => ['timestamp', 'sometimes', 'nullable'],
            'updated_at' => ['timestamp', 'sometimes', 'nullable'],
            'deleted_at' => ['timestamp', 'sometimes', 'nullable'],

        ];
    }

    public function handle($data): Note
    {
        $id         = Uuid::new();
        $data['id'] = $id;

        ClientAggregate::retrieve($data['created_by_user_id'])->createNote($current_user->id ?? "Auto Generated", $data)->persist();

        return Note::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.create', Note::class);
    }

    public function asController(ActionRequest $request)
    {
        $note = $this->handle(
            $request->validated(),
        );

        Alert::success("Note '.$note->title.' was created")->flash();

        return Redirect::route('notes.edit', $note->id);
    }

    public function __invoke($_, array $args): Note
    {
        return $this->handle($args);
    }
}
