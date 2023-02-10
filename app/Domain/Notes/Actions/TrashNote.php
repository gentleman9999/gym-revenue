<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashNote
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($current_user, $id): void
    {
        $client_id = $current_user->client_id;
        ClientAggregate::retrieve($client_id)->trashNote($current_user->id, $id)->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.trash', Note::class);
    }

    public function asController(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $this->handle(
            $request->user(),
            $id
        );

        Alert::success("Note '{$note->title}' was trashed")->flash();

        return Redirect::route('notes');
    }
}
