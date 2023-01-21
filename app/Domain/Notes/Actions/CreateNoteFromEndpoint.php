<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Aggregates\NoteAggregate;
use App\Domain\Notes\Model\Note;
use App\Domain\Users\Models\User;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateNoteFromEndpoint
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
            'id' => ['integer', 'sometimes', 'nullable'],
            'note' => ['string', 'sometimes', 'nullable'],
            'title' => ['string', 'sometimes', 'nullable'],
            'active' => ['boolean', 'sometimes', 'nullable'],
        ];
    }

    public function handle(array $data, User $current_user): Note
    {
        $id = Uuid::new();
        $client_id = $current_user->client_id;
        $data['created_by_user_id'] = $client_id;
        $data['id'] = $id;
        NoteAggregate::retrieve($client_id)->create($data)->persist();

        return Note::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.create', Note::class);
    }

    public function asController(ActionRequest $request): Note
    {
        return $this->handle(
            $request->validated(),
            $request->user(),
        );
    }

    public function htmlResponse(Note $note): RedirectResponse
    {
        Alert::success("Note '{$note->title}' was created")->flash();

        return Redirect::back();
    }
}
