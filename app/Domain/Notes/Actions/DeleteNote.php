<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Aggregates\NoteAggregate;
use App\Domain\Notes\Model\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteNote
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

    public function handle(Note $note): Note
    {
        NoteAggregate::retrieve($note->id)->delete()->persist();

        return $note;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.delete', Note::class);
    }

    public function asController(Note $note): Note
    {
        return $this->handle(
            $note
        );
    }

    public function htmlResponse(Note $note): RedirectResponse
    {
        Alert::success("Note '{$note->title}' was deleted")->flash();

        return Redirect::back();
    }
}
