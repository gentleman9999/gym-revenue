<?php

namespace App\Projectors\Users;

use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\Users\UserCreated;
use App\StorableEvents\Users\UserDeleted;
use App\StorableEvents\Users\UserUpdated;
use Bouncer;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\Database\Role;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserProjector extends Projector
{
    public function onUserCreated(UserCreated $event)
    {
        $data = $event->payload;
        //setup a transaction so we if we have errors, we don't get a half-baked user
        DB::transaction(function () use ($data, $event) {
            //get only the keys we care about (the ones marked as fillable)
            $user_table_data = array_filter($data, function ($key) {
                return in_array($key, (new User)->getFillable());
            }, ARRAY_FILTER_USE_KEY);

            $user_table_data['name'] = "{$user_table_data['first_name']} {$user_table_data['last_name']}";

            //create the entry in users table
            $user = User::create($user_table_data);

            $phone = $data['phone'] ?? null;
            if ($phone) {
                UserDetails::create(['user_id' => $user->id, 'name' => 'phone', 'value' => $phone]);
            }

            $details = [
                'altEmail' => $data['altEmail'] ?? null,
                'address1' => $data['address1'] ?? null,
                'address2' => $data['address2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip' => $data['zip'] ?? null,
                'jobTitle' => $data['jobTitle'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'termination_date' => $data['termination_date'] ?? null,
                'home_club' => $data['home_club'] ?? null,
                'is_manager' => $data['is_manager'] ?? null,
            ];

            // Go through the details and create them in the user_details via the
            // @todo - refactor other details like creating user, phone, etc to funnel through this little black hole here.
            foreach($details as $detail => $value)
            {
                UserDetails::createOrUpdateRecord($user->id, $detail, $value);
            }

            $client_id = $data['client_id'] ?? null;

            $notes = $data['notes'] ?? false;
            if($notes){
                Note::create([
                    'entity_id'=> $data['id'],
                    'entity_type'=> User::class,
                    'note' => $notes,
                    'created_by_user_id' => $event->user
                ]);
            }

            if ($client_id) {
                //setup their client association
                UserDetails::create(['user_id' => $user->id, 'name' => 'associated_client', 'value' => $client_id]);
                // Get the client's default-team name in client_details
                $client_model = Client::whereId($client_id)->with('default_team_name')->first();
                $default_team_name = $client_model->default_team_name->value;
                // Use that to find the team record in teams to get its ID
                $team = Team::find($default_team_name);
                //$team = Team::where('name', '=', $default_team_name)->first();

                // Set default_team to $client's default-team's team_id in user_details
                UserDetails::create([
                    'user_id' => $user->id,
                    'name' => 'default_team',
                    'value' => $team->id,
                    'active' => 1
                ]);

            }

            $role = null;
            $security_role = null;
            if (array_key_exists('role', $data)) {
//                dd($event->payload['role']);
                $role = Role::whereName($data['role'])->firstOrFail();
                if ($client_id) {
                    $security_role = SecurityRole::whereClientId($client_id)->whereRoleId($role->id)->first();//get default security role for role if exists
                }
            }
            else if (array_key_exists('security_role', $data) && $data['security_role']) {
                $security_role = SecurityRole::with('role')->findOrFail($data['security_role']);
                $role = $security_role->role;
            }
            else if ($data['team_id'] === 1 || $data['team_id'] === 10) {
                //set role to admin for capeandbay
                $role = Role::whereName('Admin')->firstOrFail();
            }

            //let the bouncer know this $user is OG
            Bouncer::assign($role)->to($user);
            // If the user is not an admin, disallow the full ability,
            // further down we will add the scoped abilities;
            if(!Bouncer::is($user)->an('Admin'))
            {
                $role_full_abilities = $role->abilities()->get();
                foreach($role_full_abilities as $full_ability)
                {
                    Bouncer::disallow($user)->to($full_ability);
                }
            }

            //add user security role to details
            if ($security_role) {
                UserDetails::create([
                    'user_id' => $user->id,
                    'name' => 'security_role',
                    'value' => $security_role->id
                ]);
            }

            //attach the user to their teams
            $user_teams = $data['team_ids'] ?? (array_key_exists('team_id', $data) ? [$data['team_id']] : []);

            foreach ($user_teams as $i => $team_id) {
                if ($i === 0) {
                    $user->current_team_id = $team_id;
                    $user->save();
                    UserDetails::create(['user_id' => $user->id, 'name' => 'default_team', 'value' => $team_id]);
                }

                $team = Team::findOrFail($team_id);
                $team->users()->attach(
                    $user, ['role' => $role->name]
                );

                if ($client_id) {
                    //assign security role abilities for client users only (cnb security roles not yet implemented)
                    $security_role->abilities()->each(function ($ability) use ($user) {

                        if(is_null($ability['entity_id']))
                        {
                            Bouncer::allow($user)->to($ability['ability'], $ability['entity']);
                        }
                        else
                        {
                            $entity = $ability['entity']::find($ability['entity_id']);
                            Bouncer::allow($user)->to($ability['ability'], $entity);
                        }
                    });
                }
            }
        });
    }

    public function onUserUpdated(UserUpdated $event)
    {
        $data = $event->payload;

        //setup a transaction so we if we have errors, we don't get a half-updated user
        DB::transaction(function () use ($data, $event) {
            $user = User::with(['teams', 'associated_client', 'security_role'])->findOrFail($data['id']);
            $data['name'] = "{$data['first_name']} {$data['last_name']}";

            $user->updateOrFail($data);

            $phone = $data['phone'] ?? null;
            if ($phone) {
                UserDetails::firstOrCreate(['user_id' => $user->id, 'name' => 'phone'])->updateOrFail(['value' => $phone]);
            }

            $details = [
                'altEmail' => $data['altEmail'] ?? null,
                'address1' => $data['address1'] ?? null,
                'address2' => $data['address2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip' => $data['zip'] ?? null,
                'jobTitle' => $data['jobTitle'] ?? null,
                'home_club' => $data['home_club'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'termination_date' => $data['termination_date'] ?? null,
                'is_manager' => $data['is_manager'] ?? null,
            ];

            // Go through the details and create them in the user_details via the
            // @todo - refactor other details like creating user, phone, etc to funnel through this little black hole here.
            foreach($details as $detail => $value)
            {
                UserDetails::createOrUpdateRecord($user->id, $detail, $value);
            }

            $client_id = $user->associated_client ? $user->associated_client->value : null;

            $notes = $data['notes'] ?? false;
            if($notes){
                Note::create([
                    'entity_id'=> $data['id'],
                    'entity_type'=> User::class,
                    'note' => $notes,
                    'created_by_user_id' => $event->user
                ]);
            }

            //if we were provided a security_role, update the users security_role
            if ($data['security_role'] ?? false) {
                $old_security_role = SecurityRole::with('role')->find($user->security_role->value);
                $old_role = $old_security_role->role->name;

                $security_role = SecurityRole::with('role')->find($data['security_role']);
                UserDetails::firstOrCreate(['user_id' => $user->id, 'name' => 'security_role'])->updateOrFail(['value' => $security_role->id]);

                $role = $security_role->role->name;

                //let bouncer know their role has been changed
                if ($old_role !== $role)
                {
                    Bouncer::retract($old_role)->from($user);
                    Bouncer::assign($role)->to($user);
                }

                if ($old_security_role->id !== $security_role->id)  // THIS TAKES TOO LONG
                {
                    //remove all old abilties that were assigned
                    $old_security_role->abilities()->each(function ($ability) use ($user) {
                        if($ability){
                            Bouncer::disallow($user)->to($ability['ability'], $ability['entity']);
                        }
                    });
                    //now add all new abilities
                    $security_role->abilities()->each(function ($ability) use ($user) {
                        Bouncer::allow($user)->to($ability['ability'], $ability['entity']);
                    });
                }

                //now update their team roles
                $team_roles_to_sync = [];

                $teams = $user->teams;


                foreach ($teams as $team) {
                    //only update role for teams owned by their associated client
                    $team_client = $team->client_details[0]->client;
                    if ($team_client->id === $client_id) {
                        $team_roles_to_sync[$team->id] = ['role' => $role];
                    }
                }


                //syncWithoutDetaching so CB user team associations dont get removed
                $user->teams()->syncWithoutDetaching($team_roles_to_sync);
            }
        });
    }

//    public function onUserTrashed(UserTrashed $event)
//    {
//        User::findOrFail($event->id)->delete();
//    }
//
//    public function onUserRestored(UserRestored $event)
//    {
//        User::withTrashed()->findOrFail($event->id)->restore();
//    }

    public function onUserDeleted(UserDeleted $event)
    {
        // Get the uer we're gonna delete
        $bad_user = User::findOrFail($event->payload['id']);
        // @todo - add offboading logic here

        // starting with unassigning users from teams.
        $teams = $bad_user->teams()->get();
        foreach($teams as $team)
        {
            $team->removeUser($bad_user);
        }

        $bad_user->forceDelete();
    }
}
