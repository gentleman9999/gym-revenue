<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Aggregates\NoteAggregate;
use App\Domain\Notes\Model\Note;
use App\Domain\Users\Models\User;
use App\Enums\CallOutcomesEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
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
    public function rules(): array
    {
        return [
            'id' => ['string', 'sometimes'],
            'outcome' => ['sometimes',new Enum(CallOutcomesEnum::class)],
            'note' => ['string', 'sometimes'],
            'title' => ['string', 'sometimes'],
            'active' => ['boolean', 'sometimes'],
            'callSid' => ['string', 'sometimes','nullable'],
        ];
    }

    public function handle(string $id, array $data, User $current_user): Note
    {
        $data['id'] = $id;
        $client_id = $current_user->client_id;
        $data['created_by_user_id'] = $client_id;
        NoteAggregate::retrieve($client_id)->update($data)->persist();

        return Note::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.update', Note::class);
    }

    public function asController(ActionRequest $request, string $id): Note
    {
        $data = $request->validated();

        return $this->handle(
            $id,
            $data,
            $request->user(),
        );
    }

    public function htmlResponse(Note $note): RedirectResponse
    {
        Alert::success("Note '{$note->title}' was updated")->flash();

        return Redirect::back();
    }
}
