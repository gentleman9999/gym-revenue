<?php

namespace App\Http\Controllers;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Notes\Model\Note;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     *
     */
    public function index()
    {
        $client_id = request()->user()->client_id;

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        $term = request()->term;
        if (! is_null($term)) {
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
     * @return \Illuminate\Http\Response
     */
    public function search($term)
    {
        if (! is_null($term)) {
            return $this->performSearch($term, 10);
        }

        return [];
    }

    public function search_api(Request $request)
    {
        if (! is_null($request->search)) {
            return $this->performSearch($request->search, 4);
        }

        return [];
    }

    protected function performSearch(string $term, $limit = 10)
    {
        $search = '';
        $client_id = request()->user()->client_id;
        if (! is_null($term)) {
            $search = Search::new()
                ->add(Member::where('client_id', '=', $client_id), ['first_name', 'middle_name', 'last_name', 'email', 'phone', 'alternate_phone'])
                ->add(User::where('client_id', '=', $client_id), ['first_name', 'last_name'])
                ->add(CalendarEvent::where('client_id', '=', $client_id), ['title', 'description'])
                ->add(Lead::where('client_id', '=', $client_id), ['first_name', 'middle_name', 'last_name', 'email', 'phone', 'alternate_phone'])
                ->add(Reminder::class, ['name', 'description'])
                ->add(Note::class, ['note', 'title'])
                ->add(File::where('client_id', '=', $client_id), ['filename', 'original_filename'])
                ->beginWithWildcard()
                ->includeModelType()
                ->orderByRelevance()
                ->paginate($limit)
                ->search($term);

            $search_results = $search;
            foreach ($search_results as $key => $sr) {
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
//                        TODO: this should open up the calendar event
                        $sr->link = 'calendar';//?start=' . $start . "&end=". $end . '&search='. $term;

                        break;
                    default:
                        $sr->link = $_SERVER['HTTP_REFERER'];
                }
            }
        }

        return $search_results;
    }
}
