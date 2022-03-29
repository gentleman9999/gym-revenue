<?php

namespace App\Actions\Impersonation;

use App\Models\Role;
use App\Models\UserDetails;
use Bouncer;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUsers
{
    use AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {
        // ...
    }

    public function handle()
    {
        $results = [];

        $user = auth()->user();
        $user_role = Role::whereName($user->getRoles()[0])->first()->title;

        switch($user_role)
        {
            case 'Admin':
                $allowed_roles = ['Admin', 'Account Owner', 'Regional Admin', 'Location Manager', 'Sales Rep', 'Employee'];
                break;

            case 'Administrator':
                $allowed_roles = ['Administrator', 'Account Owner', 'Regional Admin', 'Location Manager', 'Sales Rep', 'Employee'];
                break;

            case 'Account Owner':
                $allowed_roles = ['Account Owner', 'Regional Admin', 'Location Manager', 'Sales Rep', 'Employee'];
                break;

            case 'Regional Admin':
                $allowed_roles = ['Regional Admin', 'Location Manager', 'Sales Rep', 'Employee'];
                break;

            case 'Location Manager':
                $allowed_roles = ['Sales Rep', 'Employee'];
                break;

            case 'Sales Rep':
            case 'Employee':
            default:
                $allowed_roles = [];
        }
        // Get the User's currently active team
        $current_team = $user->currentTeam()->first();

        // If the team is a default_team, then get all users for that client
        if($current_team->default_team)
        {
            $client_detail = $current_team->client_details()->first();
            if(is_null($client_detail))
            {
                // This is a CnB Team
                $imp_users = User::all();
                foreach($imp_users as $imp_user)
                {
                    if(Bouncer::is($imp_user)->an('Admin'))
                    {
                        $results[] = $imp_user;
                    }
                }
            }
            else
            {
                // This is a client team
                $client = $client_detail->client;
                $user_details = UserDetails::where('name', '=', 'associated_client')
                    ->whereValue($client->id)->with('user')->whereActive(1)
                    ->get();

                if(count($user_details) > 0)
                {
                    foreach ($user_details as $user_detail)
                    {
                        if(!is_null($user_detail->user))
                        {
                            $results[] = $user_detail->user;
                        }
                    }
                }
            }
        }
        else
        {
            // get the users for that team
            $imp_users = $current_team->team_users()->get();

            foreach($imp_users as $imp_user)
            {
                if(!is_null($imp_user->user))
                {
                    $results[] = $imp_user->user;
                }
            }
        }

        if(count($results) > 0)
        {
            $potential_imp_users = $results;
            $results = [];

            if(count($allowed_roles) > 0)
            {
                foreach ($potential_imp_users as $potential_imp_user)
                {
                    // Filter out the the logged in user from the results
                    if($potential_imp_user->id != $user->id)
                    {
                        //filter out team_users in roles above the user
                        foreach ($allowed_roles as $allowed_role)
                        {
                            if(Bouncer::is($potential_imp_user)->an($potential_imp_user->currentClientId().' '.$allowed_role))
                            {

                                $results[] = [
                                    'userId' => $potential_imp_user->id,
                                    'name' => $potential_imp_user->name,
                                    'role' => Role::whereName($potential_imp_user->getRoles()[0])->first()->title
                                ];
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $results;
    }

    public function jsonResponse($result)
    {
        $results = false;
        $code = 500;

        if(count($result) > 0)
        {
            if(request()->user()->can('users.impersonate', User::class))
            {
                $code = 200;
                $results = $result;
            }
        }

        return response($results, $code);
    }
}
