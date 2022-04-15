<?php

namespace App\Actions\Clients\Tasks;

use App\Aggregates\Users\UserAggregate;
use App\Models\Tasks;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreTask
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

    public function handle($data, $user=null)
    {
        UserAggregate::retrieve($user->id ?? "Auto Generated", $data['client_id'])
            ->applyTaskRestored()
            ->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('task.restore', Tasks::class);
    }

    public function asController(Request $request, $id)
    {
        $task = Tasks::withTrashed()->findOrFail($id);

        $this->handle(
            $task->toArray(),
            $request->user()
        );

        Alert::success("Task '{$task->title}' restored.")->flash();

        return Redirect::back();
    }
}
