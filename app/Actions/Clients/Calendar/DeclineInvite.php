<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarEvent;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class DeclineInvite
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
        ];
    }

    public function handle($data)
    {

        $client_id = $data->client;
        $data = $data->data;
        $eventData = CalendarEvent::whereId($data['calendar_event_id'])->first();
        //$attendeeData = CalendarAttendee::

        CalendarAggregate::retrieve($client_id)
            ->declineCalendarEvent("Auto Generated", $data)
            ->persist();


        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $attendee = $this->handle(
            $request->validated()
        );

        Alert::success("Invitation '{$attendee->title}' Declined!")->flash();

        return Redirect::back();
    }

}
