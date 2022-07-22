<?php

namespace App\Actions\Clients\Notes;

use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreNote
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($current_user, $id)
    {
        $note = Note::findOrFail($id);

        $client_id = $current_user->client_id;
        ClientAggregate::retrieve($client_id)->restoreNote($current_user->id, $id)->persist();

        return $note;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('note.restore', Note::class);
    }

    public function asController(Request $request, $id)
    {
        $note = $this->handle(
            $request->user(),
            $id
        );
        Alert::success("Note '{$note->title}' restored.")->flash();

        return Redirect::route('notes');
    }
}
