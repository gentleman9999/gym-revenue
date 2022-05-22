<?php

namespace App\Actions\Clients\Tasks;

use App\Aggregates\Users\UserAggregate;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteTask
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

    public function handle($id, User $user)
    {
        UserAggregate::retrieve($user->id)
            ->deleteTask($user->id ?? "Auto Generated", $id)
            ->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('task.update', Tasks::class);
    }

    public function asController(Request $request, $id)
    {
        $task = $this->handle(
            $id,
            $request->user()
        );

        Alert::success("Task '{$task->title}' was deleted")->flash();

        return Redirect::back();
    }
}
