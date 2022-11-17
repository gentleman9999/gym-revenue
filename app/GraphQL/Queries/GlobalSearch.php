<?php

namespace App\GraphQL\Queries;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\User;
use App\Models\File;
use App\Models\Note;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

final class GlobalSearch
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $client_id = request()->user()->client_id;
        if (! is_null($args['term'])) {
            $search = Search::new()
                ->add(Member::where('client_id', '=', $client_id), ['first_name', 'middle_name', 'last_name', 'email', 'primary_phone', 'alternate_phone'])
                ->add(User::where('client_id', '=', $client_id), ['first_name', 'last_name'])
                ->add(CalendarEvent::where('client_id', '=', $client_id), ['title', 'description'])
                ->add(Lead::where('client_id', '=', $client_id), ['first_name', 'middle_name', 'last_name', 'email', 'primary_phone', 'alternate_phone'])
                ->add(Reminder::class, ['name', 'description'])
                ->add(Note::class, ['note', 'title'])
                ->add(File::where('client_id', '=', $client_id), ['filename', 'original_filename'])
                ->beginWithWildcard()
                ->includeModelType()
                ->orderByRelevance()
                ->paginate($perPage = $args['pagination']['limit'], $pageName = 'page', $page = $args['pagination']['page'])
                ->search($args['term']);

            $result = [];
            $result['paginatorInfo'] = [
                'currentPage' => $search->currentPage(),
                'lastPage' => $search->lastPage(),
                'firstItem' => $search->firstItem(),
                'lastItem' => $search->lastItem(),
                'perPage' => $search->perPage(),
                'total' => $search->total(),
            ];
            $result['data'] = $this->parseResult($search);

            return $result;
        }

        return [];
    }

    private function parseResult($result)
    {
        foreach ($result as $key => $sr) {
            $attributes = $sr->getAttributes();
            if (! array_key_exists('name', $attributes) || $attributes["name"] == "") {
                $name = $sr->name;
                if (array_key_exists('title', $attributes)) {
                    $sr->name = $sr->title;
                } elseif (array_key_exists("description", $attributes)) {
                    $sr->name = $sr->description;
                }
            }
            switch ($sr->type) {
                case "Lead":
                case "Member":
                    $sr->link = 'data.' . strtolower($sr->type) . 's.edit';

                    break;
                case "File":
                case "Reminder":
                case "Note":
                case "User":
                    $sr->link = strtolower($sr->type) . 's.edit';

                    break;
                case "CalendarEvent":
                    $sr->link = 'calendar';

                    break;
                default:
                    $sr->link = $_SERVER['HTTP_REFERER'];
            }
        }

        return $result;
    }
}
