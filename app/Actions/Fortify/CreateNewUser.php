<?php

namespace App\Actions\Fortify;

use App\Models\Clients\Security\SecurityRole;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Actions\Jetstream\AddTeamMember;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\Database\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param array $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'team' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) use ($input) {
                /**
                 * STEPS After the User is created
                 * 1.
                 * 2. Take the team name, find the record, then find add a new record to team users
                 * 3. Alternatively, use the JetStream action AddTeamMember
                 * 4. Use bouncer to assign user to the role mentioned
                 */

                // Take the team name, find the record, then find add a new record to team users
                $team = Team::whereName($input['team'])->first();

                UserDetails::create([
                    'user_id' => $user->id,
                    'name' => 'default_team',
                    'value' => $team->id
                ]);

                $team->users()->attach(
                    $user, ['role' => $input['role']]
                );

                $user->current_team_id = $team->id;
                $user->save();

                // Use bouncer to assign user to the role mentioned
                Bouncer::assign($input['role'])->to($user);

                if ($input['client'] !== 'Cape & Bay') {
                    //If the client is not Cape & Bay, add a record to this user's user details saying the client
                    $client = Client::whereName($input['client'])->first();
                    UserDetails::create([
                        'user_id' => $user->id,
                        'name' => 'associated_client',
                        'value' => $client->id
                    ]);
                    //add security role
                    $role = Role::whereName($input['role'])->first();
                    if ($role) {
                        $security_role = SecurityRole::whereClientId($client->id)->whereRoleId($role->id)->first();//get default security role for role if exists
                        if ($security_role) {
                            UserDetails::create([
                                'user_id' => $user->id,
                                'name' => 'security_role',
                                'value' => $security_role->id
                            ]);
                        }
                    }
                }
            });
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        // @todo - update this
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }
}
