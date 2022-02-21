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
        ];
        $user = auth()->user();
        $phone = $user->phone_number()->first();
        if(!is_null($phone))
        {
            $addl_data['phone'] = $phone->value;
        }

        $altEmail = $user->altEmail()->first();
        if(!is_null($altEmail))
        {
            $addl_data['altEmail'] = $altEmail->value;
        }

        $address1 = $user->address1()->first();
        if(!is_null($address1))
        {
            $addl_data['address1'] = $address1->value;
        }

        $address2 = $user->address2()->first();
        if(!is_null($address2))
        {
            $addl_data['address2'] = $address2->value;
        }

        $city = $user->city()->first();
        if(!is_null($city))
        {
            $addl_data['city'] = $city->value;
        }

        $state = $user->state()->first();
        if(!is_null($state))
        {
            $addl_data['state'] = $state->value;
        }

        $zip = $user->zip()->first();
        if(!is_null($zip))
        {
            $addl_data['zip'] = $zip->value;
        }

        $jobTitle = $user->jobTitle()->first();
        if(!is_null($jobTitle))
        {
            $addl_data['jobTitle'] = $jobTitle->value;
        }

        return Jetstream::inertia()->render($request, 'Profile/Show', [
            'sessions' => $this->sessions($request)->all(),
            'addlData' => $addl_data
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
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
