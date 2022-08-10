<?php

namespace App\Domain\CalendarAttendees\Actions;

use App\Actions\ShortUrl\CreateShortUrl;
use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendeeAggregate;
use App\Models\ShortUrl;
use Lorisleiva\Actions\Concerns\AsAction;

class InviteAttendee
{
    use AsAction;

    public function handle(CalendarAttendee $calendarAttendee): CalendarAttendee
    {
//        $data = $data->data;
        $eventData = $calendarAttendee->event;

        $route = 'invite/'.$calendarAttendee->id;

        $shortUrl = ShortUrl::whereRoute($route)->first();

        if (is_null($shortUrl)) {
            $shortUrl = CreateShortUrl::run(['route' => 'invite/'.$calendarAttendee->id], $calendarAttendee->client_id);
        }

//        $data['subject'] = 'GR-CRM Event Invite for '.$eventData->title;
//        $data['body'] = '<h1>'.$eventData->title.'</h1> <p>You have been invited!</p> <a href="'.env('APP_URL').'/s/'.$shortUrl->external_url.'">Click here to accept or decline.</a>';

        CalendarAttendeeAggregate::retrieve($calendarAttendee->id)->invite()->persist();

        return $calendarAttendee->refresh();
    }
}
