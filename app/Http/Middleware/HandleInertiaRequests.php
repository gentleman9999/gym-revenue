<?php

namespace App\Http\Middleware;

use App\Models\Utility\AppState;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Prologue\Alerts\Facades\Alert;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request)
    {
        $shared = [];
        $user = $request->user();
        if ($request->user()) {
            $abilities = $request->user()->getAbilities()->filter(function ($ability) use ($request) {
                if (! is_null($ability->entity_id)) {
                    $r = $ability->entity_id === $request->user()->current_team_id;
                } elseif ($ability->title == 'All abilities') {
                    $r = true;
                } else {
                    $r = true;
                }

                return $r;
            })->pluck('name');
            $shared = [
                'user.id' => $user->id,
                'user.all_locations' => $user->allLocations(),
                'user.current_client_id' => $user->currentClientId(),
                'user.abilities' => $abilities,
                'user.has_api_token' => (! is_null($user->access_token)),
                'app_state.is_simulation_mode' => AppState::isSimuationMode(),
                'user.column_config' => $user->column_config->mapWithKeys(function ($item, $key) {
                    return [$item['value'] => $item['misc']];
                }),

            ];

            if (session()->has(config('laravel-impersonate.session_key'))) {
                $shared['user.is_being_impersonated'] = session()->get(config('laravel-impersonate.session_key'));
            }
        }
        $previousUrl = url()->previous();

        if (! empty($previousUrl) && $previousUrl !== route('login') && $previousUrl !== url()->current()) {
            $shared['previousUrl'] = $previousUrl;
        }
        $alerts = Alert::getMessages();

        return array_merge(parent::share($request), [
            'flash' => function () use ($request, $alerts) {
                return [
                    'selectedLeadDetailIndex' => $request->session()->get('selectedLeadDetailIndex'),
                    'alerts' => $alerts,
                ];
            },
        ], $shared);


//        $shared['flash'] = [];
//        if(!empty($request->session()->get('selectedLeadDetailIndex'))){
//            dd('works');
//            $shared['flash']['selectedLeadDetailIndex'] = $request->session()->get('selectedLeadDetailIndex');
//        }
////        $shared[ 'flash'] = ['selectedLeadDetailIndex'=>$request->session()->get('selectedLeadDetailIndex')];
//        $shared['flash']['foo'] = 'bar';
//        return array_merge(parent::share($request), $shared);
    }

    //This code makes redirects worth with inertia modals
    public function handle(Request $request, Closure $next)
    {
        $response = parent::handle($request, $next);

        if ($response instanceof RedirectResponse && $request->hasHeader('X-Inertia-Modal-Redirect-Back')) {
//            dd($response);
            return back(303);
        }

        if ($request->hasHeader('X-Inertia-Modal')) {
            $response->headers->set('X-Inertia-Modal', $request->header('X-Inertia-Modal'));
        }

        return $response;
    }
}
