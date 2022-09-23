<?php

namespace App\Domain\Campaigns\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Campaigns\CallOutcomeAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateCallOutcome
{
    use AsAction;

    public function handle(array $payload, CalendarEvent $calendarEvent): CalendarEvent
    {
        $id = Uuid::new();

        $aggy = CallOutcomeAggregate::retrieve($id);

        $aggy->create($payload)->persist();

        return $calendarEvent->refresh();
    }

    public function rules(): array
    {
        return [
            'outcome' => ['required', 'string'],
            'id' => ['required', 'string'],
            'lead_attendees' => ['sometimes'],
            'member_attendees' => ['sometimes'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request, CalendarEvent $calendarEvent): CalendarEvent
    {
        $data = $request->validated();
        $data['client_id'] = request()->user()->client_id;

        return $this->handle(
            $data,
            $calendarEvent,
        );
    }

    public function htmlResponse(CalendarEvent $calendarEvent): RedirectResponse
    {
        Alert::success("Call Outcome '{$calendarEvent->title}' was updated")->flash();

        return Redirect::back();
    }
}
