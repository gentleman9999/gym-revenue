<?php

namespace App\GraphQL\Queries;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use DateTime;

final class TasksQuery
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $client_id = request()->user()->client_id;
        if (! key_exists('start', $args['param'])) {
            $args['param']['start'] = date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format('Y-m-d 00:00:00')
            )->getTimestamp());
            $args['param']['end'] = date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format('Y-m-d 23:59:59')
            )->getTimestamp());
        } else {
            $date = date('Y-m-d', strtotime($args['param']['start']));
            $args['param']['start'] = date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format($date . ' 00:00:00')
            )->getTimestamp());
            $args['param']['end'] = date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format($date . ' 23:59:59')
            )->getTimestamp());
        }
        $typeTaskForClient = CalendarEventType::whereClientId($client_id)
            ->whereType('Task')
            ->first();
        if (! is_null($typeTaskForClient)) {
            switch ($args['param']['type']) {
                case 'incomplete_tasks':
                    $tasks = CalendarEvent::with('owner')
                        ->whereEventTypeId($typeTaskForClient->id)
                        ->whereOwnerId(request()->user()->id)
                        ->whereNull('event_completion')
                        ->with('type')
                        ->filter($args['param'])
                        ->paginate(10, ['*'], 'page', $args['pagination']['page']);

                    break;
                case 'completed_tasks':
                    $tasks = CalendarEvent::with('owner')
                        ->whereEventTypeId($typeTaskForClient->id)
                        ->whereOwnerId(request()->user()->id)
                        ->whereNotNull('event_completion')
                        ->with('type')
                        ->filter($args['param'])
                        ->paginate(10, ['*'], 'page', $args['pagination']['page']);

                    break;
                case 'overdue_tasks':
                    $tasks = CalendarEvent::with('owner')
                        ->whereEventTypeId($typeTaskForClient->id)
                        ->whereOwnerId(request()->user()->id)
                        ->whereNull('event_completion')
                        ->whereDate('start', '<', date('Y-m-d H:i:s'))
                        ->with('type')
                        ->filter($args['param'])
                        ->paginate(10, ['*'], 'page', $args['pagination']['page']);

                    break;
            }

            return [
                'data' => $tasks,
                'paginatorInfo' => $this->getPagination($tasks),
            ];
        } else {
            return [
                'data' => [],
                'paginatorInfo' => [
                    'currentPage' => 0,
                    'lastPage' => 0,
                    'firstItem' => 0,
                    'lastItem' => 0,
                    'perPage' => 10,
                    'total' => 0,
                ],
            ];
        }
    }

    private function getPagination($search)
    {
        return [
            'currentPage' => $search->currentPage(),
            'lastPage' => $search->lastPage(),
            'firstItem' => $search->firstItem(),
            'lastItem' => $search->lastItem(),
            'perPage' => $search->perPage(),
            'total' => $search->total(),
        ];
    }
}
