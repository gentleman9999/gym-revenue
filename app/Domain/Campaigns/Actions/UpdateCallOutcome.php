<?php

namespace App\Domain\Campaigns\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Campaigns\CallOutcomeAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateCallOutcome
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
            'outcome' => ['required', 'string'],
            'outcomeId' => ['required', 'string'],
            'id' => ['required', 'string'],
            'lead_attendees' => ['sometimes'],
            'member_attendees' => ['sometimes'],
        ];
    }

    public function handle(array $data, CalendarEvent $calendarEvent)
    {
        $aggy = CallOutcomeAggregate::retrieve($data['outcomeId']);
        $data['client_id'] = request()->user()->client_id;
        $aggy->update($data)->persist();

        return $calendarEvent->refresh();
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

        return $this->handle(
            $data,
            $calendarEvent,
        );
    }

    public function htmlResponse(CalendarEvent $calendarEvent): RedirectResponse
    {
        Alert::success("Calendar Event '{$calendarEvent->title}' was updated")->flash();

        return Redirect::back();
    }
}
