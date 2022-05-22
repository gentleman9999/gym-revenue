<?php

namespace App\Actions\Clients\Tasks;

use App\Aggregates\Users\UserAggregate;
use App\Models\Tasks;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Twilio\TwiML\Voice\Task;

class MarkTaskComplete
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
            'title' => ['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'user_id' => ['sometimes'],
            'due_at' => ['sometimes'],
            'completed_at' => ['sometimes'],
            ];
    }

    public function handle($id, $user)
    {
        UserAggregate::retrieve($user->id)
            ->markTaskAsComplete($user->id ?? "Auto Generated", $id)
            ->persist();

        return Task::find($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('task.update', Tasks::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $task = $this->handle(
            $id,
            $request->user()
        );

        Alert::success("Task '{$task->title}' was updated")->flash();

        return Redirect::back();
    }
}
