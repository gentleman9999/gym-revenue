<?php

namespace App\Domain\Users\Actions;

use App\Domain\Clients\Models\Client;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use function auth;
use Lorisleiva\Actions\Concerns\AsAction;
use function request;
use function response;

class GetUsersToImpersonate
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
        $user_role = $user->getRole();


        switch ($user_role) {
            case 'Admin':
                $allowed_roles = [SecurityGroupEnum::ADMIN, SecurityGroupEnum::ACCOUNT_OWNER, SecurityGroupEnum::REGIONAL_ADMIN, SecurityGroupEnum::LOCATION_MANAGER, SecurityGroupEnum::SALES_REP, SecurityGroupEnum::EMPLOYEE];

                break;

            case 'Account Owner':
                $allowed_roles = [SecurityGroupEnum::ACCOUNT_OWNER, SecurityGroupEnum::REGIONAL_ADMIN, SecurityGroupEnum::LOCATION_MANAGER, SecurityGroupEnum::SALES_REP, SecurityGroupEnum::EMPLOYEE];

                break;

            case 'Regional Admin':
                $allowed_roles = [SecurityGroupEnum::REGIONAL_ADMIN, SecurityGroupEnum::LOCATION_MANAGER, SecurityGroupEnum::SALES_REP, SecurityGroupEnum::EMPLOYEE];

                break;

            case 'Location Manager':
                $allowed_roles = [SecurityGroupEnum::SALES_REP, SecurityGroupEnum::EMPLOYEE];

                break;

            case 'Sales Rep':
            case 'Employee':
            default:
                $allowed_roles = [];
        }

        $victim_users = [];

        $clients = Client::all();
        foreach ($clients as $client) {
            $teams = Team::withoutGlobalScopes()->whereClientId($client->id)->get();

            foreach ($teams as $team) {
                $victim_users[$client->name][$team->name] = $team->team_users()->get();
            }
        }//idk what to do with this

        // Get the User's currently active team
        $current_team = $user->currentTeam()->first();

        // If the team is a default_team, then get all users for that client
        if ($current_team->home_team) {
            if (is_null($current_team->client)) {
                // This is a CnB Team
                $imp_users = User::all();
                foreach ($imp_users as $imp_user) {
                    if ($imp_user->inSecurityGroup(SecurityGroupEnum::ADMIN)) {
                        $results[] = $imp_user;
                    }
                }
            } else {
                // This is a client team
                $client = $current_team->client;

                $client_users = $current_team->client->users;

                if (count($client_users) > 0) {
                    foreach ($client_users as $client_user) {
                        $results[] = $client_user;
                    }
                }
            }
        } else {
            // get the users for that team
            $imp_users = $current_team->team_users()->get();

            foreach ($imp_users as $imp_user) {
                if (! is_null($imp_user->user)) {
                    $results[] = $imp_user->user;
                }
            }
        }

        if (count($results) > 0) {
            $potential_imp_users = $results;
            $results = [];

            if (count($allowed_roles) > 0) {
                foreach ($potential_imp_users as $potential_imp_user) {
                    // Filter out the the logged in user from the results
                    if ($potential_imp_user->id != $user->id) {
                        //filter out team_users in roles above the user
                        foreach ($allowed_roles as $allowed_role) {
                            if ($potential_imp_user->inSecurityGroup($allowed_role)) {
                                $results[] = [
                                    'userId' => $potential_imp_user->id,
                                    'name' => $potential_imp_user->name,
                                    'role' => $potential_imp_user->getRole(),
                                    'group' => $potential_imp_user->role()->group,
                                ];

                                break;
                            }
                        }
                    }
                }
            }
        }
        $results = collect($results)->sortBy('group')->toArray();
        $counter = 0;
        foreach ($results as $result) {
            unset($result['group']);
            $sortedArray[$counter] = $result;
            $counter++;
        }
        $results = $sortedArray;

        return $results;
    }

    public function jsonResponse($result)
    {
        $results = false;
        $code = 500;

        if (count($result) > 0) {
            if (request()->user()->can('users.impersonate', User::class)) {
                $code = 200;
                $results = $result;
            }
        }

        return response($results, $code);
    }
}
