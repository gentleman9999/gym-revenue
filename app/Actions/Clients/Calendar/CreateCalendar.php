<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateCalendar
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
            'title' =>['required'],
            'full_day_event' => ['required'],
            'start' => ['required'],
            'end' => ['required'],
            'type' => ['required'],
        ];
    }

    public function handle($data)
    {
        $id = Uuid::new();
        $data['id'] = $id;  // TO-DO Add ID into create
        CalendarAggregate::retrieve($data['client_id'])
            ->createCalendarEvent($data['title'], $data['full_day_event'], $data['start'], $data['end'], $data['type'])
            ->persist();

        return Location::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.create', CalendarEvent::class);
    }

    public function asController(ActionRequest $request)
    {

        $calendar = $this->handle(
            $request->validated()
        );

        Alert::success("Calendar Event '{$calendar->name}' was created")->flash();

        return Redirect::route('locations');
    }

}
