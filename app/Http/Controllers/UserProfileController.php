<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Laravel\Jetstream\Jetstream;

class UserProfileController extends Controller
{
    /**
     * Show the general profile settings screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $addl_data = [
            'phone' => '',
            'address1' => '',
            'address2' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            'jobTitle' => '',
            'altEmail' => '',
            'start_date' => '',
            'communication_preference' => '',
        ];
        $user = auth()->user();
        $phone = $user->phone;
        if (! is_null($phone)) {
            $addl_data['phone'] = $phone->value;
        }

        $altEmail = $user->altEmail()->first();
        if (! is_null($altEmail)) {
            $addl_data['altEmail'] = $altEmail->value;
        }

        $address1 = $user->address1;
        if (! is_null($address1)) {
            $addl_data['address1'] = $address1->value;
        }

        $address2 = $user->address2;
        if (! is_null($address2)) {
            $addl_data['address2'] = $address2->value;
        }

        $city = $user->city;
        if (! is_null($city)) {
            $addl_data['city'] = $city->value;
        }

        $state = $user->state;
        if (! is_null($state)) {
            $addl_data['state'] = $state->value;
        }

        $zip = $user->zip;
        if (! is_null($zip)) {
            $addl_data['zip'] = $zip->value;
        }

        $jobTitle = $user->jobTitle()->first();
        if (! is_null($jobTitle)) {
            $addl_data['jobTitle'] = $jobTitle->value;
        }
        $start_date = $user->start_date()->first();
        if (! is_null($start_date)) {
            $addl_data['start_date'] = $start_date->value;
        }
        $end_date = $user->end_date()->first();
        if (! is_null($end_date)) {
            $addl_data['end_date'] = $end_date->value;
        }
        $termination_date = $user->termination_date()->first();
        if (! is_null($termination_date)) {
            $addl_data['termination_date'] = $termination_date->value;
        }
        $notes = $user->notes()->first();
        if (! is_null($notes)) {
            $addl_data['notes'] = $notes->value;
        }

        $contact_preference = $user->contact_preference()->first();
        if (! is_null($contact_preference)) {
            $addl_data['contact_preference'] = $contact_preference->value;
        }

        $api_token = $user->api_token()->first();
        if (! is_null($api_token)) {
            $addl_data['token'] = base64_decode($api_token->value);
        }

        return Jetstream::inertia()->render($request, 'Profile/Show', [
            'sessions' => $this->sessions($request)->all(),
            'addlData' => $addl_data,
        ]);
    }

    /**
     * Get the current sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function sessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $request->user()->getAuthIdentifier())
                    ->orderBy('last_activity', 'desc')
                    ->get()
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent(), function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
