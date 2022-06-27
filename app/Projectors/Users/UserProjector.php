<?php

namespace App\Projectors\Users;

use App\Models\Calendar\CalendarEvent;
use App\Models\Clients\Client;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Tasks;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use App\StorableEvents\Clients\Tasks\TaskMarkedComplete;
use App\StorableEvents\Clients\Tasks\TaskMarkedIncomplete;
use App\StorableEvents\Clients\Tasks\TaskTrashed;
use App\StorableEvents\Clients\Tasks\TaskUpdated;
use App\StorableEvents\Users\Notifications\NotificationCreated;
use App\StorableEvents\Users\Notifications\NotificationDismissed;
use App\StorableEvents\Users\UserCreated;
use App\StorableEvents\Users\UserDeleted;
use App\StorableEvents\Users\UserSetCustomCrudColumns;
use App\StorableEvents\Users\UserUpdated;
use Bouncer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                return in_array($key, (new User())->getFillable());
            }, ARRAY_FILTER_USE_KEY);

            //create the entry in users table
            $user = User::create($user_table_data);

            $details = [
                'contact_preference' => $data['contact_preference'] ?? 'sms', //default sms
            ];

            // Go through the details and create them in the user_details via the
            // @todo - refactor other details like creating user, phone, etc to funnel through this little black hole here.
            foreach ($details as $detail => $value) {
                UserDetails::createOrUpdateRecord($user->id, $detail, $value);
            }

            $client_id = $data['client_id'] ?? null;

            $notes = $data['notes'] ?? false;
            if ($notes) {
                Note::create([
                    'entity_id' => $data['id'],
                    'entity_type' => User::class,
                    'title' => $notes['title'],
                    'note' => $notes['note'],
                    'created_by_user_id' => $event->user,
                ]);
            }

            if ($client_id) {
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
                    'active' => 1,
                ]);
            }

            /** Users have:
             * A Role that contain abilities
             * A classification which is a fancy word for title (employee position)
             * These two declarations should never EVER be chained together.
             */
            $role = null;
            if (array_key_exists('role', $data)) {
                $role = Role::whereId($data['role'])->get();
            }
            if (array_key_exists('team_id', $data)) {
                if ($data['team_id'] === 1 || $data['team_id'] === 10) {
                    //set role to admin for capeandbay
                    $role = Role::whereName('Admin')->firstOrFail();
                }
            }

            //let the bouncer know this $user is OG
            Bouncer::assign($role)->to($user);

            //attach the user to their teams
            $user_teams = $data['team_ids'] ?? (array_key_exists('team_id', $data) ? [$data['team_id']] : []);

            foreach ($user_teams as $i => $team_id) {
                if ($i === 0) {
                    $user->current_team_id = $team_id;
                    $user->save();
                    UserDetails::create(['user_id' => $user->id, 'name' => 'default_team', 'value' => $team_id]);
                }

                $team = Team::findOrFail($team_id);
                $team->users()->attach($user);
            }
        });
    }

    public function onUserUpdated(UserUpdated $event)
    {
        $data = $event->payload;

        //setup a transaction so we if we have errors, we don't get a half-updated user
        DB::transaction(function () use ($data, $event) {
            $user = User::with(['teams'])->findOrFail($data['id']);
            $data['name'] = "{$data['first_name']} {$data['last_name']}";

            $user->updateOrFail($data);

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
                    'entity_id' => $data['id'],
                    'entity_type' => User::class,
                    'title' => $notes['title'],
                    'note' => $notes['note'],
                    'created_by_user_id' => $event->user,
                ]);
            }

            if ($data['role'] ?? false) {
                $old_role = $user->getRole();

                $role = Role::whereId($data['role'])->get();
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
        $bad_user = User::findOrFail($event->payload['id']);
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
            'user_id' => $event->user,
            'name' => "column-config",
            'value' => $event->table,
        ])->update(['misc' => $event->fields]);
    }

    public function onNotificationCreated(NotificationCreated $event)
    {
        Log::debug($event->data);
        Notification::create(array_merge($event->data, ['user_id' => $event->user]));
    }

    public function onNotificationDismissed(NotificationDismissed $event)
    {
        //TODO:check if event->createdAt is preserved after replays.  If not,
        //we just need to track "dismissed_at" in the NotificationDismissed event itself
        Notification::findOrFail($event->id)->updateOrFail(['dismissed_at' => $event->createdAt()]);
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
