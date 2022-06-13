<?php

namespace App\Domain\Reminders\Actions;

use App\Domain\Reminders\Reminder;
use App\Domain\Users\UserAggregate;
use App\Helpers\Uuid;
use App\Models\Calendar\CalendarEvent;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateReminderFromCalendarEvent
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
        if (! is_null($current_user)) {
            $client_id = $current_user->currentClientId();
            $data['client_id'] = $client_id;
        }

        $id = Uuid::new();
        $data['id'] = $id;
        $data['entity_type'] = CalendarEvent::class;
        $data['user_id'] = $current_user->id;
        $data['name'] = 'User Generated Reminder';
        $data['remind_time'] = 30;

        UserAggregate::retrieve($data['user_id'])->createReminder($current_user->id, $data)->persist();

        return Reminder::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['entity_id'] = $id;
        $reminder = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Reminder '{$reminder->name}' was created")->flash();

        return Redirect::back();
    }
}
