<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Models\File;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse|RedirectResponse
    {
        $client_id = request()->user()->client_id;

        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        $term = request()->term;
        if ($term !== null) {
            $search = $this->search($term);
            // Adding the term to the path so the pagination works correctly
            $search->setPath($search->path() . '?term=' . $term);
        } else {
            $search = "";
        }

        return Inertia::render('Search/Show', [
            'term' => $term,
            'results' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function search(?string $term = null): LengthAwarePaginator|Collection
    {
        if ($term !== null) {
            return $this->performSearch($term, 10);
        }

        return new Collection();
    }

    public function searchApi(Request $request): LengthAwarePaginator|Collection
    {
        if ($request->search !== null) {
            return $this->performSearch($request->search, 4);
        }

        return new Collection();
    }

    protected function performSearch(string $term, int $limit = 10): LengthAwarePaginator|Collection
    {
        $request        = request();
        $client_id      = $request->user()->client_id;
        $search_results = new Collection();

        if ($term !== null) {
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
                ->paginate($limit)
                ->search($term);

            $search_results = $search;
            foreach ($search_results as $sr) {
                $attributes = $sr->getAttributes();
                if (! array_key_exists('name', $attributes) || $attributes["name"] == "") {
                    if (array_key_exists('title', $attributes)) {
                        $sr->name = $sr->title;
                    } elseif (array_key_exists("description", $attributes)) {
                        $sr->name = $sr->description;
                    }
                }
                $sr->link = match ($sr->type) {
                    'Lead', 'Member' => 'data.' . strtolower($sr->type) . 's.edit',
                    'File', 'Reminder', "Note", "User" => strtolower($sr->type) . 's.edit',
                    'CalendarEvent' => 'calendar',
                    default => $request->headers->get('referer'),
                };
            }
        }

        return $search_results;
    }
}
