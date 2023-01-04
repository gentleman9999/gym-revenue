<?php

namespace App\Domain\Reminders\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
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
    public function rules(): array
    {
        return [
            'id' => ['string', 'sometimes'],
        ];
    }

    public function handle(array $data): Reminder
    {
        $id = Uuid::new();
        $data['id'] = $id;
        $data['entity_type'] = CalendarEvent::class;
        $data['name'] = 'User Generated Reminder';
        $data['remind_time'] = 30;

        UserAggregate::retrieve($data['user_id'])->createReminder($data)->persist();

        return Reminder::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    //TODO: implement real authorization
    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request, Reminder $reminder): RedirectResponse
    {
        $data = $request->validated();
        $data['entity_id'] = request()->id;
        $data['user_id'] = $request->user()->id;
        $reminder = $this->handle(
            $data,
        );

        Alert::success("Reminder '{$reminder->name}' was created")->flash();

        return Redirect::back();
    }
}
