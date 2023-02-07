<?php

namespace App\GraphQL\Queries;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;

//TODO: remove this in favor of a standard db query
//TODO: 1.) stop separating the attendees into different arrays, instead do that clientside by filtering on attendeee type
//TODO: 2.) remove this class in favor of just using a CalendarEvent @find query
final class CalendarEvents
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        $client_id = request()->user()->client_id;
        $arg_param_exists = array_key_exists('param', $args);

        if ($arg_param_exists && ! key_exists('start', $args['param'])) {
            $args['param']['start'] = date('Y-m-d H:i:s', strtotime('-1 week monday 00:00:00'));
            $args['param']['end'] = date('Y-m-d H:i:s', strtotime('sunday 23:59:59'));
        }

        $events = CalendarEvent::whereClientId($client_id)
            ->with('type', 'attendees', 'files')
            ->when($arg_param_exists,  function ($query) use ($args) {
                $query->filter($args['param']);
            })
            ->get();

        foreach ($events as $key => $event) {
            $user_attendees = [];
            $lead_attendees = [];
            $member_attendees = [];
            if ($event->attendees) {
                foreach ($event->attendees as $attendee) {
                    if ($attendee->entity_type == User::class) {
                        if (request()->user()->id == $attendee->entity_id) {
                            $events[$key]['my_reminder'] = Reminder::whereEntityType(CalendarEvent::class)
                                ->whereEntityId($event['id'])
                                ->whereUserId($attendee->entity_id)
                                ->first();

                            $events[$key]['im_attending'] = true;
                        }
                        $user_attendees[] = [
                            'id' => (int)$attendee->entity_id,
                            'reminder' => Reminder::whereEntityType(CalendarEvent::class)
                                ->whereEntityId($event['id'])
                                ->whereUserId($attendee->entity_id)
                                ->first() ?? null,
                        ];
                    }

                    if ($attendee->entity_type == Lead::class) {
                        $lead_attendees[]['id'] = $attendee->entity_id;
                        //TODO: Create Projection Table for User Communication History
//                        $call_outcome = EndUserDetails::whereField('call_outcome')
//                            ->whereEndUserId($attendee->entity_id)
//                            ->whereEntityId($event->id)->first();
                    }
                    if ($attendee->entity_type == Member::class) {
                        $member_attendees[]['id'] = $attendee->entity_id;

                        //TODO: Create Projection Table for User Communication History
//                        $call_outcome = EndUserDetails::whereField('call_outcome')
//                            ->whereEndUserId($attendee->entity_id)
//                            ->whereEntityId($event->id)->first();
                    }
                    if ($event->call_task == 1) {
                        if (isset($call_outcome)) {
                            $event->callOutcome = $call_outcome['value'];
                            $event->callOutcomeId = $call_outcome['id'];
                        }
                    }
                }
            }
            $events[$key]->user_attendees = $user_attendees;
            $events[$key]->lead_attendees = $lead_attendees;
            $events[$key]->member_attendees = $member_attendees;
        }

        return $events;
    }
}
