<?php

namespace App\Actions\Clients\Tasks;

use App\Aggregates\Users\UserAggregate;
use App\Models\Calendar\CalendarEvent;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

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
            'id' => ['string', 'sometimes'],
        ];
    }

    public function handle($data, $current_user)
    {
        UserAggregate::retrieve($current_user->id)->markTaskAsComplete($current_user->id, $data['id'])->persist();

        return CalendarEvent::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $task = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Task '{$task->title}' marked complete!")->flash();

        return Redirect::back();
    }
}
