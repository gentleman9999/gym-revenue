<?php

declare(strict_types=1);

namespace App\Actions\Clients\Tasks;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\User;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use App\Support\Uuid;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateTask
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
            'title' => ['required', 'string', 'max:50'],
            'description' => ['string', 'nullable'],
            'user_id' => ['sometimes'],
            'due_at' => ['sometimes'],
            'completed_at' => ['sometimes'],
        ];
    }

    public function handle($data, User $user)
    {
        $id         = Uuid::new();
        $data['id'] = $id;

        UserAggregate::retrieve($user->id)
            ->createTask($user->id ?? "Auto Generated", $data)
            ->persist();

        return CalendarEvent::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('task.create', TaskCreated::class);
    }

    public function asController(ActionRequest $request)
    {
        $task = $this->handle(
            $request->validated(),
            $request->user()
        );

        Alert::success("Task'{$task->title}' was created")->flash();

        return Redirect::back();
    }
}
