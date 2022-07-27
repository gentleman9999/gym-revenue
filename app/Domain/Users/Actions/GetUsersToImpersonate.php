<?php

namespace App\Domain\Users\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use function auth;
use Lorisleiva\Actions\ActionRequest;
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

    public function rules(): array
    {
        return [
            'team' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function handle(ActionRequest $request): array
    {
        try {
            $data = $request->validated();

            $user = auth()->user();
            $user_role = $user->getRole();

            // Get the User's currently active team
            $session_team = session()->get('current_team');
            if ($session_team && array_key_exists('id', $session_team)) {
                $team = Team::find($session_team['id']);
            } else {
                $team = Team::find($user->default_team_id);
            }

            if (array_key_exists('team', $data)) {
                if ($user->inSecurityGroup(SecurityGroupEnum::ADMIN)) {
                    $team = Team::withoutGlobalScopes()->findOrFail($data['team']);
                } else {
                    $team = Team::findOrFail($data['team']);
                }
            }

            $users = [];
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

            $users = [];

            // get the users for that team
            $imp_users = $team->team_users()->get();

            foreach ($imp_users as $imp_user) {
                if (! is_null($imp_user->user)) {
                    $users[] = $imp_user->user;
                } else {
                    $users[] = User::withoutGlobalScopes()->findOrFail($imp_user->user_id);
                }
            }

            if (count($users) > 0) {
                $potential_imp_users = $users;
                $users = [];

                if (count($allowed_roles) > 0) {
                    foreach ($potential_imp_users as $potential_imp_user) {
                        // Filter out the the logged in user from the results
                        if ($potential_imp_user->id != $user->id) {
                            //filter out team_users in roles above the user
                            foreach ($allowed_roles as $allowed_role) {
                                if ($potential_imp_user->inSecurityGroup($allowed_role)) {
                                    $users[] = [
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

            $users = collect($users)->sortBy('group')->toArray();

            $counter = 0;
            $sortedArray = [];
            foreach ($users as $result) {
                unset($result['group']);
                $sortedArray[$counter] = $result;
                $counter++;
            }

            return $sortedArray;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function authorize(): bool
    {
        return request()->user()->can('users.impersonate', User::class);
    }

    public function jsonResponse($result)
    {
        $results = false;
        $code = 500;

        if (count($result) > 0) {
            $code = 200;
            $results = $result;
        }

        return response($results, $code);
    }
}
