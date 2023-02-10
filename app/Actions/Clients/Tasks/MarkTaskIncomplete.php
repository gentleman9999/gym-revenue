<?php

declare(strict_types=1);

namespace App\Actions\Clients\Tasks;

use App\Aggregates\Users\UserAggregate;
use App\Domain\Users\Models\User;
use App\Models\Tasks;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Twilio\TwiML\Voice\Task;

class MarkTaskIncomplete
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'user_id' => ['sometimes'],
            'due_at' => ['sometimes'],
            'completed_at' => ['sometimes'],
        ];
    }

    public function handle(string $id, User $user): Tasks
    {
        UserAggregate::retrieve($id)
            ->applyTaskMarkedIncomplete($user->id ?? "Auto Generated", $id)
            ->persist();

        return Task::find($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('task.update', Tasks::class);
    }

    public function asController(ActionRequest $request, string $id)
    {
        $task = $this->handle(
            $id,
            $request->user()
        );

        Alert::success("Task '{$task->title}' was updated")->flash();

        return Redirect::back();
    }
}
