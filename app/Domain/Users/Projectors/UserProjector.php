<?php

namespace App\Domain\Users\Projectors;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Events\AccessTokenGranted;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserDeleted;
use App\Domain\Users\Events\UserSetCustomCrudColumns;
use App\Domain\Users\Events\UserUpdated;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserDetails;
use App\Enums\SecurityGroupEnum;
use App\Models\Note;
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
            $user = new User();

            $user->id = $event->aggregateRootUuid();
            $user->client_id = $event->payload['client_id'];
            if (array_key_exists('password', $event->payload)) {
                $user->password = $event->payload['password'];
            }
            $user_table_data = array_filter($data, function ($key) {
                return in_array($key, (new User())->getFillable());
            }, ARRAY_FILTER_USE_KEY);
            $user->fill($user_table_data);

            $user->save();

            $details = [
                'contact_preference' => $data['contact_preference'] ?? 'sms', //default sms
            ];

            // Go through the details and create them in the user_details via the
            // @todo - refactor other details like creating user, phone, etc to funnel through this little black hole here.
            foreach ($details as $detail => $value) {
                UserDetails::createOrUpdateRecord($user->id, $detail, $value);
            }

//            $client_id = $data['client_id'] ?? null;

            //TODO: use an action that trigger ES specific to note
            $notes = $data['notes'] ?? false;
            if ($notes) {
                Note::create([
                    'entity_id' => $event->aggregateRootUuid(),
                    'entity_type' => User::class,
                    'title' => $notes['title'],
                    'note' => $notes['note'],
                    'created_by_user_id' => $event->userId(),
                ]);
            }

//            if ($client_id) {
//                // Get the client's default-team name in client_details
//                $client_model = Client::find($client_id);
//                $default_team_name = $client_model->default_team_name->value;
//                // Use that to find the team record in teams to get its ID
//                $team = Team::find($default_team_name);
//                //$team = Team::where('name', '=', $default_team_name)->first();
//
//                // Set default_team to $client's default-team's team_id in user_details
//                UserDetails::create([
//                    'user_id' => $user->id,
//                    'name' => 'default_team',
//                    'value' => $team->id,
//                    'active' => 1,
//                ]);
//            }

            /** Users have:
             * A Role that contain abilities
             * A classification which is a fancy word for title (employee position)
             * These two declarations should never EVER be chained together.
             */
            $role = null;
            if (array_key_exists('role_id', $data)) {
                $role = Role::find($data['role_id']);
            }
            //if team_id is set, and its  non-client team, make the user's role an admin
            //TODO:change this to look at the current team.
            if (array_key_exists('team_id', $data)) {
                if (Team::findOrFail($data['team_id'])->client_id === null) {
                    //set role to admin for capeandbay
                    $role = Role::whereGroup(SecurityGroupEnum::ADMIN)->firstOrFail();
                    $user->is_cape_and_bay_user = true;
                    $user->save();
                }
            }

            if ($role) {
                //let the bouncer know this $user is OG
                Bouncer::assign($role)->to($user);
            }

            //attach the user to their teams
            $user_teams = $data['team_ids'] ?? (array_key_exists('team_id', $data) ? [$data['team_id']] : []);

            foreach ($user_teams as $i => $team_id) {
                if ($i === 0) {
                    $user->current_team_id = $team_id;
                    $user->save();
                    UserDetails::create(['user_id' => $user->id, 'name' => 'default_team', 'value' => $team_id]);
                }

//                $team = Team::findOrFail($team_id);
//                $team->users()->attach($user);
            }
        });
    }

    public function onUserUpdated(UserUpdated $event)
    {
        $data = $event->payload;

        //setup a transaction so we if we have errors, we don't get a half-updated user
        DB::transaction(function () use ($data, $event) {
            if ($event->userId() == $event->aggregateRootUuid()) {
                //updating our own userprofile, remove client scope
                $user = User::withoutGlobalScopes()->with(['teams'])->findOrFail($event->aggregateRootUuid());
            } else {
                $user = User::with(['teams'])->findOrFail($event->aggregateRootUuid());
            }

            $user->updateOrFail($data);

            if (array_key_exists('positions', $data)) {
                $user->positions()->sync($data['positions']);
            }

            if (array_key_exists('departments', $data)) {
                $user->departments()->sync($data['departments']);
            }

            $details = [
                'contact_preference' => $data['contact_preference'] ?? null,
            ];

            // Go through the details and create them in the user_details via the
            // @todo - refactor other details like creating user, phone, etc to funnel through this little black hole here.
            foreach ($details as $detail => $value) {
                UserDetails::createOrUpdateRecord($user->id, $detail, $value);
            }

            $notes = $data['notes'] ?? false;
            if ($notes) {
                Note::create([
                    'entity_id' => $event->aggregateRootUuid(),
                    'entity_type' => User::class,
                    'title' => $notes['title'],
                    'note' => $notes['note'],
                    'created_by_user_id' => $event->userId(),
                ]);
            }

            if ($data['role'] ?? false) {
                $old_role = $user->getRole();

                $role = Role::find($data['role']);
                //let bouncer know their role has been changed
                if ($old_role !== $role) {
                    Bouncer::retract($old_role)->from($user);
                    Bouncer::assign($role)->to($user);
                }

                //now update their team roles
                $team_roles_to_sync = [];

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
        $bad_user = User::findOrFail($event->aggregateRootUuid());
        // @todo - add offboading logic here

        // starting with unassigning users from teams.
        $teams = $bad_user->teams()->get();
        foreach ($teams as $team) {
            $team->removeUser($bad_user);
        }

        $bad_user->forceDelete();
    }

    public function onUserSetCustomCrudColumns(UserSetCustomCrudColumns $event)
    {
        UserDetails::firstOrCreate([
            'user_id' => $event->aggregateRootUuid(),
            'name' => "column-config",
            'value' => $event->table,
        ])->update(['misc' => $event->fields]);
    }

    public function onAccessTokenGranted(AccessTokenGranted $event)
    {
        $user = User::findOrFail($event->aggregateRootUuid());
        $user->tokens()->delete();
        $token = $user->createToken($user->email)->plainTextToken;
        $user = User::findOrFail($user->id);
        $user->forceFill(['access_token' => base64_encode($token)]);
        $user->save();
    }

    public function onTaskCreated(TaskCreated $event)
    {
        Tasks::create($event->data);
    }

    public function onTaskUpdated(TaskUpdated $event)
    {
        Tasks::findOrFail($event->data['id'])->update($event->data);
    }

    public function onTaskDeleted(TaskDeleted $event)
    {
        Tasks::withTrashed()->findOrFail($event->data['id'])->forceDelete();
    }

    public function onTaskRestored(TaskRestored $event)
    {
        Tasks::withTrashed()->findOrFail($event->data['id'])->restore();
    }

    public function onTaskTrashed(TaskTrashed $event)
    {
        Tasks::findOrFail($event->data['id'])->delete();
    }

    public function onTaskMarkedComplete(TaskMarkedComplete $event)
    {
        CalendarEvent::findOrFail($event->id)->update(['event_completion' => $event->createdAt()]);
    }

    public function onTaskMarkedIncomplete(TaskMarkedIncomplete $event)
    {
        Tasks::findOrFail($event->data['id'])->update(['completed_at' => null]);
    }
}
