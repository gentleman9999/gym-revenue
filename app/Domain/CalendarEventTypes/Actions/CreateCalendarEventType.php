<?php

declare(strict_types=1);

namespace App\Domain\CalendarEventTypes\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\CalendarEventTypes\CalendarEventTypeAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateCalendarEventType
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
            'name' => ['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'type' => ['required', 'string', 'nullable'],
            'color' => ['required', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): CalendarEventType
    {
        $id = Uuid::get();

        CalendarEventTypeAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return CalendarEventType::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.create', CalendarEvent::class);
    }

    public function asController(ActionRequest $request): CalendarEventType
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(CalendarEventType $calendar_event_type): RedirectResponse
    {
        Alert::success("Calendar Event Type '{$calendar_event_type->name}' was created")->flash();

        return Redirect::route('calendar.event_types.edit', $calendar_event_type->id);
    }
}
