<?php

namespace App\Actions\Clients\Locations;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateLocation
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
        CalendarAggregate::retrieve($data['client_id'])
            ->updateCalendarEvent($data['id'], $data['title'], $data['full_day_event'], $data['start'], $data['end'], $data['type'])
            ->persist();

        return Location::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $calendar = $this->handle(
            $data
        );

        Alert::success("Calendar Event '{$calendar->name}' was updated")->flash();

        return Redirect::back();
    }

}
