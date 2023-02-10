<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Models\File;
use App\Models\Note;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

final class GlobalSearch
{
    private function parseResult(LengthAwarePaginator|Collection $result): LengthAwarePaginator|Collection
    {
        foreach ($result as $sr) {
            $attributes = $sr->getAttributes();
            if (! array_key_exists('name', $attributes) || $attributes["name"] === "") {
                if (array_key_exists('title', $attributes)) {
                    $sr->name = $sr->title;
                } elseif (array_key_exists("description", $attributes)) {
                    $sr->name = $sr->description;
                }
            }

            $sr->link = match ($sr->type) {
                'Lead', 'Member' => 'data.' . strtolower($sr->type) . 's.edit',
                'File', 'Reminder', 'Note', 'User' => strtolower($sr->type) . 's.edit',
                'CalendarEvent' => 'calendar',
                default => request()->headers->get('referer'),
            };
        }

        return $result;
    }

    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     *
     * @return array<string, mixed>
     */
    public function __invoke($_, array $args): array
    {
        $client_id = request()->user()->client_id;
        if ($args['term'] !== null) {
            $search = Search::new()
                ->add(Member::where('client_id', '=', $client_id), [
                    'first_name',
                    'middle_name',
                    'last_name',
                    'email',
                    'phone',
                    'alternate_phone',
                ])
                ->add(User::where('client_id', '=', $client_id), ['first_name', 'last_name'])
                ->add(CalendarEvent::where('client_id', '=', $client_id), ['title', 'description'])
                ->add(Lead::where('client_id', '=', $client_id), [
                    'first_name',
                    'middle_name',
                    'last_name',
                    'email',
                    'phone',
                    'alternate_phone',
                ])
                ->add(Reminder::class, ['name', 'description'])
                ->add(Note::class, ['note', 'title'])
                ->add(File::where('client_id', '=', $client_id), ['filename', 'original_filename'])
                ->beginWithWildcard()
                ->includeModelType()
                ->orderByRelevance()
                ->paginate($args['pagination']['limit'], 'page', $args['pagination']['page'])
                ->search($args['term']);

            $result                  = [];
            $result['paginatorInfo'] = [
                'currentPage' => $search->currentPage(),
                'lastPage' => $search->lastPage(),
                'firstItem' => $search->firstItem(),
                'lastItem' => $search->lastItem(),
                'perPage' => $search->perPage(),
                'total' => $search->total(),
            ];
            $result['data']          = $this->parseResult($search);

            return $result;
        }

        return [];
    }
}
