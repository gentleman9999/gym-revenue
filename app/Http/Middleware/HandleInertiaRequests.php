<?php

namespace App\Http\Middleware;

use App\Domain\Clients\Projections\Client;
use App\Enums\SecurityGroupEnum;
use App\Enums\UserTypesEnum;
use App\Models\Utility\AppState;
use App\Support\CurrentInfoRetriever;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
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
            $current_team_id = CurrentInfoRetriever::getCurrentTeamID();
            $abilities = $user->getAbilities()->filter(function ($ability) use ($user, $current_team_id) {
                if (! is_null($ability->entity_id)) {
                    $r = $ability->entity_id === $current_team_id;
                } elseif ($ability->title == 'All abilities') {
                    $r = true;
                } else {
                    $r = true;
                }

                return $r;
            })->pluck('name');

            $client = Client::with(['trial_membership_types', 'locations'])->find($user->client_id);
            $shared = [
                'user.id' => $user->id,
                'user.contact_preference' => $user->contact_preference,
                'user.all_locations' => $user->user_type === UserTypesEnum::EMPLOYEE ? $user->allLocations() : null,
                'user.current_team.isClientTeam' => $user->client_id !== null,
                'user.is_client_user' => $user->client_id !== null,
                'user.is_gr_admin' => $user->inSecurityGroup(SecurityGroupEnum::ADMIN),
                'user.current_team_id' => session()->get('current_team')['id'] ?? null,
                'user.current_team_id1' => session()->get('current_team_id'),
                //TODO:should be able to remove client_id from most of client stuff once middleware is in place
                'user.abilities' => $abilities,
                'user.has_api_token' => (! is_null($user->access_token)),
                'app_state.is_simulation_mode' => AppState::isSimuationMode(),
                'client_services' => $client->services ?? null,
//                TODO: Query this from the CRUD when it is being initialized
                'user.column_config' => $user->column_config->mapWithKeys(function ($item, $key) {
                    return [$item->value => $item->misc];
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
            'jetstream' => function () use ($request) {
                return [
                    'canCreateTeams' => $request->user() &&
                        Jetstream::hasTeamFeatures() &&
                        Gate::forUser($request->user())->check('create', Jetstream::newTeamModel()),
                    'canManageTwoFactorAuthentication' => Features::canManageTwoFactorAuthentication(),
                    'canUpdatePassword' => Features::enabled(Features::updatePasswords()),
                    'canUpdateProfileInformation' => Features::canUpdateProfileInformation(),
                    'hasEmailVerification' => Features::enabled(Features::emailVerification()),
                    'flash' => $request->session()->get('flash', []),
                    'hasAccountDeletionFeatures' => Jetstream::hasAccountDeletionFeatures(),
                    'hasApiFeatures' => Jetstream::hasApiFeatures(),
                    'hasTeamFeatures' => Jetstream::hasTeamFeatures(),
                    'hasTermsAndPrivacyPolicyFeature' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
                    'managesProfilePhotos' => Jetstream::managesProfilePhotos(),
                ];
            },
            'user' => function () use ($request) {
                if (! $request->user()) {
                    return;
                }

                return array_merge($request->user()->toArray(), array_filter([
                    'all_teams' => Jetstream::hasTeamFeatures() ? (
                        $request->user()->user_type === UserTypesEnum::EMPLOYEE ?
                        $request->user()->allTeams()->values() : null
                    ) : null,
                ]), [
                    'two_factor_enabled' => ! is_null($request->user()->two_factor_secret),
                ]);
            },
            'errorBags' => function () {
                return collect(optional(Session::get('errors'))->getBags() ?: [])->mapWithKeys(function ($bag, $key) {
                    return [$key => $bag->messages()];
                })->all();
            },
            'errors' => function () use ($request) {
                return $this->resolveValidationErrors($request);
            },
        ], [
            'flash' => function () use ($request, $alerts) {
                return [
                    'selectedLeadDetailIndex' => $request->session()->get('selectedLeadDetailIndex'),
                    'selectedMemberDetailIndex' => $request->session()->get('selectedMemberDetailIndex'),
                    'alerts' => $alerts,
                ];
            },
        ], $shared);
    }

    //This code makes redirects worth with inertia modals
    public function handle(Request $request, Closure $next)
    {
        $response = parent::handle($request, $next);

        if ($request->hasHeader('X-Inertia-Modal')) {
            $response->headers->set('X-Inertia-Modal', $request->header('X-Inertia-Modal'));
        }

        if ($response instanceof RedirectResponse && ($request->hasHeader('X-Inertia-Modal-Redirect-Back'))) {
//            dd($response);
            return back(303);
        }

        return $response;
    }
}
