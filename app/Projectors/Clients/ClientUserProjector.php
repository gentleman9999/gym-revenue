<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\Clients\UserCreated;
use App\StorableEvents\Clients\UserDeleted;
use App\StorableEvents\Clients\UserUpdated;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\Database\Role;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Bouncer;

class ClientUserProjector extends Projector
{
    public function onUserCreated(UserCreated $event)
    {
        $data = $event->payload;
//        dd($event->payload);
        //setup a transaction so we if we have errors, we don't get a half-baked user
        DB::transaction(function () use ($data) {
            //get only the keys we care about (the ones marked as fillable)
            $user_table_data = array_filter($data, function ($key) {
                return in_array($key, (new User)->getFillable());
            }, ARRAY_FILTER_USE_KEY);
            //create the entry in users table
            $user = User::create($user_table_data);
            $client_id = $data['client_id'];

            $role = null;
            $security_role = null;
            if (array_key_exists('role', $data)) {
//                dd($event->payload['role']);
                $role = Role::whereName($data['role'])->firstOrFail();
                if ($client_id) {
                    $security_role = SecurityRole::whereClientId($client_id)->whereRoleId($role->id)->first();//get default security role for role if exists
                }
            } else if (array_key_exists('security_role', $data)) {
                $security_role = SecurityRole::with('role')->findOrFail($data['security_role']);
                $role = $security_role->role;
            }

            if ($security_role) {
                UserDetails::create([
                    'user_id' => $user->id,
                    'name' => 'security_role',
                    'value' => $security_role->id
                ]);
            }

            $user_teams = $data['teams'] ?? [$data['team']] ?? [];

            foreach ($user_teams as $i => $team_id) {
                if ($i === 0) {
                    $user->current_team_id = $team_id;
                    $user->save();
                    UserDetails::create(['user_id' => $user->id, 'name' => 'default_team', 'value' => $team_id]);
                }

                Team::findOrFail($team_id)->users()->attach(
                    $user, ['role' => $role->name]
                );
            }


            if ($client_id) {
                UserDetails::create(['user_id' => $user->id, 'name' => 'associated_client', 'value' => $client_id]);
                // Get the client's default-team name in client_details
                $client_model = Client::whereId($client_id)->with('default_team_name')->first();
                $default_team_name = $client_model->default_team_name->value;
                // Use that to find the team record in teams to get its ID
                $team = Team::where('name', '=', $default_team_name)->first();

                // Set default_team to $client's default-team's team_id in user_details
                UserDetails::create([
                    'user_id' => $user->id,
                    'name' => 'default_team',
                    'value' => $team->id,
                    'active' => 1
                ]);
            }
//            $current_team = $user->currentTeam()->first();
//            UserDetails::create(['user_id' => $user->id, 'name' => 'default_team', 'value' => $current_team->id]);
//            $current_team->users()->attach(
//                $user, ['role' => $role]
//            );
        });
    }

    public function onUserUpdated(UserUpdated $event)
    {
        $data = $event->payload;
        //setup a transaction so we if we have errors, we don't get a half-updated user
        DB::transaction(function () use ($data) {
            $user = User::with(['teams', 'associated_client'])->findOrFail($data['id']);
            $user->updateOrFail($data);
            if ($data['security_role']) {
                $security_role = SecurityRole::with('role')->find($data['security_role']);
                UserDetails::firstOrCreate(['user_id' => $user->id, 'name' => 'security_role'])->updateOrFail(['value' => $security_role->id]);
                $role = $security_role->role->name;
                $user->teams()->sync([$data['team'] => ['role' => $role]]);//TODO: check if this wipes other teams
            }
            $client_id = $user->associated_client ? $user->associated_client->id : null;
            $teams = $user->teams;
            foreach($teams as $team){
                //update role on all client teams if it changed
                $team_client = $team->client_details[0]->client;
//                dd($team_client->id, $client_id);
                if($team_client->id === $client_id){
                    $team_role = $team->pivot->role;
                    dd($team_role);
//                    if($team_role !== )
//                    if ($client_id && $role !== $old_role) {
//                        $aggy = ClientAggregate::retrieve($client_id);
//                        $aggy->updateUserRoleOnTeam($user->id, $current_team->id, $old_role, $role);
//                        $aggy->persist();
//                    }
                }
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
        User::findOrFail($event->payload['id'])->forceDelete();
    }
}
