<?php

namespace App\Domain\CalendarAttendees\Actions;

use App\Actions\ShortUrl\CreateShortUrl;
use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendeeAggregate;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeAdded;
use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Users\Models\EndUser;
use App\Models\ShortUrl;
use Lorisleiva\Actions\Concerns\AsAction;

class InviteAttendee
{
    use AsAction;

    public function handle(CalendarAttendeeAdded $data): ?CalendarAttendee
    {
        $calendar_attendee = EndUser::find($data->payload['entity_id']);
        if (! is_null($calendar_attendee)) {
            $event = CalendarEvent::find($data->payload['calendar_event_id']);
            $route = 'invite/'.$calendar_attendee->id;

            $short_url = ShortUrl::whereRoute($route)->first();

            if (is_null($short_url)) {
                $short_url = CreateShortUrl::run(['route' => $route], $calendar_attendee->client_id);
            }

            $data->payload['subject'] = 'GR-CRM Event Invite for '.$event->title;
            $data->payload['body'] = '<h1>'.$event->title.'</h1> <p>You have been invited!</p> <a href="'.env('APP_URL').'/s/'.$short_url->external_url.'">Click here to accept or decline.</a>';
            $data->payload['endUser'] = $calendar_attendee->toArray();

            CalendarAttendeeAggregate::retrieve($calendar_attendee->id)->invite($data->payload)->persist();
        }

        return $calendar_attendee?->refresh();
    }
}
