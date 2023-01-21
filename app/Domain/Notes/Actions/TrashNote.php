<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Aggregates\NoteAggregate;
use App\Domain\Notes\Model\Note;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
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

    public function handle(Note $note): Note
    {
        NoteAggregate::retrieve($note->id)->trash()->persist();

        return $note->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.trash', Note::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, $id): Note
    {
        return $this->handle(
            $id,
        );
    }

    public function htmlResponse(Note $note): RedirectResponse
    {
        Alert::success("Team '{$note->title}' was sent to the trash.")->flash();

        return Redirect::route('teams');
    }
}
