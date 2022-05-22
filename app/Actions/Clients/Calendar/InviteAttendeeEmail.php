<?php

namespace App\Actions\Clients\Calendar;

use App\Actions\ShortUrl\CreateShortUrl;
use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarAttendee;
use App\Models\Calendar\CalendarEvent;
use App\Models\ShortUrl;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class InviteAttendeeEmail
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

        $attendee = CalendarAttendee::whereEntityType($data['entity_type'])
            ->whereEntityId($data['entity_id'])
            ->whereCalendarEventId($data['calendar_event_id'])
            ->first();

        $route = 'invite/'.$attendee->id;

        $shortUrl = ShortUrl::whereRoute($route)->first();

        if (is_null($shortUrl)) {
            CreateShortUrl::run(['route' => 'invite/'.$attendee->id], $client_id);
            $shortUrl = ShortUrl::whereRoute($route)->first();
        }

        $data['subject'] = 'GR-CRM Event Invite for '.$eventData->title;
        $data['body'] = '<h1>'.$eventData->title.'</h1> <p>You have been invited!</p> <a href="'.env('APP_URL').'/s/'.$shortUrl->external_url.'">Click here to accept or decline.</a>';


        CalendarAggregate::retrieve($client_id)
            ->inviteCalendarAttendee("Auto Generated", $data)
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

        Alert::success("Attendee '{$attendee->name}' was invited to the scheduled event.")->flash();

        return Redirect::back();
    }
}
