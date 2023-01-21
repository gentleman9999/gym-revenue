<?php

declare(strict_types=1);

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Aggregates\NoteAggregate;
use App\Domain\Notes\Model\Note;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\User;
use App\Enums\CallOutcomesEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateNoteFromContactCall
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
            'method' => ['string', 'required'],
            'outcome' => ['required', new Enum(CallOutcomesEnum::class)],
            'notes' => ['string', 'required'],
        ];
    }

    public function handle(array $data, User $current_user, Lead $lead): Note
    {
        $id = Uuid::new();
        $client_id = $current_user->client_id;

        $payload = [
            'id' => $id,
            'title' => $data['outcome'] . '-(' . $lead->first_name . ' ' . $lead->last_name . ')',
            'note' => $data['notes'],
            'entity_type' => Lead::class,
            'entity_id' => $lead->id,
            'category' => strtoupper(preg_replace('/-/', '_', $data['outcome'])),
            'created_by_user_id' => $client_id,
        ];
        NoteAggregate::retrieve($client_id)->create($payload)->persist();

        return Note::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('notes.create', Note::class);
    }

    public function asController(ActionRequest $request, string $end_user_id): Note
    {
        $lead = Lead::find($end_user_id);

        return $this->handle(
            $request->validated(),
            $request->user(),
            $lead
        );
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function htmlResponse(Note $note): RedirectResponse
    {
        Alert::success("Note '{$note->title}' was created")->flash();

        return Redirect::back();
    }
}
