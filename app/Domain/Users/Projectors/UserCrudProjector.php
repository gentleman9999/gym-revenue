<?php

declare(strict_types=1);

namespace App\Domain\Users\Projectors;

use App\Domain\LocationEmployees\Actions\CreateLocationEmployee;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamDetail;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserTerminated;
use App\Domain\Users\Events\UserUpdated;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use App\Enums\UserTypesEnum;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserCrudProjector extends Projector
{
    /**
     * Array used to identify what values we want to store in the detail JSON field
     * for different user_types. The key is the field value and the value
     * is the default value for the specified key
     */
//    TODO: Why are we setting contact_preference to SMS here?  It should be NULL or UNSET, and default to the CLIENT SETTING
    private $user_details_storable = [
        'employee' => [
            'contact_preference' => 'sms',
            'emergency_contact' => ['ec_first_name' => '', 'ec_last_name' => '', 'ec_phone' => ''],
            'default_team_id' => null,
        ],
        'lead' => [
            'contact_preference' => 'sms',
            'emergency_contact' => ['ec_first_name' => '', 'ec_last_name' => '', 'ec_phone' => ''],
            'claimed' => null,
            'owner_user_id' => '',
            'converted_at' => '',
            'membership_type_id' => '',
        ],
        'customer' => [
            'contact_preference' => 'sms',
            'emergency_contact' => ['ec_first_name' => '', 'ec_last_name' => '', 'ec_phone' => ''],
            'membership_type_id' => '',
        ],
        'member' => [
            'contact_preference' => 'sms',
            'emergency_contact' => ['ec_first_name' => '', 'ec_last_name' => '', 'ec_phone' => ''],
            'membership_type_id' => '',
        ],
    ];
    private $non_fillable_fields = [
        'email', 'barcode', 'home_location_id', 'external_id', 'misc', 'client_id',
        'profile_photo_path', 'alternate_emails', 'user_type', 'password',
        'two_factor_secret', 'two_factor_recovery_codes', 'ip_address',
        'entry_source', 'opportunity', 'started_at', 'ended_at',
        'terminated_at', 'agreement_id', 'is_previous',
    ];

    public function onStartingEventReplay()
    {
        User::truncate();
    }

    public function onUserCreated(UserCreated $event): void
    {
        $data = $event->payload;

        if (array_key_exists('phone', $data)) {
            $data['phone'] = "{$data['phone']}";
        }

        //setup a transaction so we if we have errors, we don't get a half-baked user
        DB::transaction(function () use ($data, $event) {
            $user = new User();
            $user->id = $event->aggregateRootUuid();
            foreach ($this->non_fillable_fields as $key => $field) {
                if (array_key_exists($field, $data)) {
                    $user[$field] = $data[$field];
                }
            }
            $user_table_data = array_filter($data, function ($key) {
                return in_array($key, (new User())->getFillable());
            }, ARRAY_FILTER_USE_KEY);
            $user->fill($user_table_data);
            $user->save();
            $user = User::findOrFail($event->aggregateRootUuid());

            if (array_key_exists('departments', $data)) {
                $this->syncLocationEmployees($user, $data);
            }

            $this->setUserDetails($user, $data);

            $notes = $data['notes'] ?? false;
            if ($notes) {
                $this->createUserNotes($event, $user, $notes);
            }

            /**
             * Users have a Role that contain abilities
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
                $team = Team::find($data['team_id']);
                if ($team && $team->client_id == null) {
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

            $this->setUserDetails($user, $data);
        });
    }

    public function onUserUpdated(UserUpdated $event): void
    {
        $data = $event->payload;
        if (array_key_exists('phone', $data)) {
            $data['phone'] = "{$data['phone']}";
        }

        //setup a transaction so we if we have errors, we don't get a half-updated user
        DB::transaction(function () use ($data, $event) {
            $user_query = User::query();
            /** updating our own userprofile, remove client scope */
            if ($event->userId() == $event->aggregateRootUuid()) {
                $user_query = User::withoutGlobalScopes();
            }
            $user = $user_query->findOrFail($event->aggregateRootUuid());
            foreach ($this->non_fillable_fields as $key => $field) {
                if (array_key_exists($field, $data)) {
                    $user[$field] = $data[$field];
                }
            }

            $user->save();
            $user->updateOrFail($data);

            if (array_key_exists('departments', $data)) {
                $this->syncLocationEmployees($user, $data);
            }

            $this->setUserDetails($user, $data, true);

            $notes = $data['notes'] ?? false;
            if ($notes) {
                $this->createUserNotes($event, $user, $notes);
            }

            if (array_key_exists('role_id', $data)) {
                $old_role = $user->getRole();

                $role = Role::find($data['role_id']);
                //let bouncer know their role has been changed
                if ($old_role !== $role) {
                    Bouncer::retract($old_role)->from($user);
                    Bouncer::assign($role)->to($user);
                }

                //now update their team roles
                $team_roles_to_sync = [];

                //syncWithoutDetaching so CB user team associations dont get removed
                if ($user->user_type === UserTypesEnum::EMPLOYEE) {
                    $user->teams()->syncWithoutDetaching($team_roles_to_sync);
                }
            }
        });
    }

    public function onEndUserReinstated(UserReinstated $event): void
    {
        User::withTrashed()->findOrFail($event->id)->reinstate();
    }

    public function onUserTerminated(UserTerminated $event): void
    {
        // Get the uer we're gonna delete
        $user = User::findOrFail($event->aggregateRootUuid());
        // @todo - add offboading logic here

        // starting with unassigning users from teams.
        $teams = $user->teams()->get();
        foreach ($teams as $team) {
            $team->removeUser($user);
        }

        $user->terminate();
    }

    protected function syncLocationEmployees(User $user, array $data): void
    {
        $location_employee_data['location_id'] = '';
        $location_employee_data['client_id'] = $user->client_id;
        $location_employee_data['user_id'] = $user->id;
        $location_employee_data['primary_supervisor_user_id'] = '';

        if (count($data) != count($data, COUNT_RECURSIVE)) {
            if (array_key_exists('departments', $data)) {
                foreach ($data['departments'] as $dept) {
                    $location_employee_data['department_id'] = $dept['department'];
                    $location_employee_data['position_id'] = $dept['position'];
                    CreateLocationEmployee::run($location_employee_data);
                }
            }
        }
    }

    /**
     * Create UserDetails based on values passed in $data
     *
     * @param User $user
     * @param array $data
     * @param bool $is_updating
     */
    protected function setUserDetails(User $user, array $data, bool $is_updating = false): void
    {
        $details = [];
        $user_type = $this->getUserType($user, $data);
        if ($this->getUserType($user, $data) == UserTypesEnum::EMPLOYEE) {
            $default_team = $this->getDefaultTeamId($user, $data, $is_updating);
            if ($default_team) {
                $data['default_team_id'] = $default_team;
            }
        }

        foreach ($this->user_details_storable[$this->getUserType($user, $data)->value] as $field_value => $default_value) {
            $value = $data[$field_value] ?? null;
            if (! $is_updating) {
                $value = $value ?? $default_value;
            }

            if ($value || ! $is_updating) {
                $details[$field_value] = $value;
            }
        }
//        TODO: we can just store this at the same time as the rest of the user data, no need for 2 updates
        $user->details = $details;
        $user->save();
    }

    protected function createUserNotes($event, User $user, array $notes): void
    {
        foreach ($notes as $note) {
            if ($notes['title'] != null) {
                Note::create([
                    'entity_id' => $event->aggregateRootUuid(),
                    'entity_type' => User::class,
                    'title' => $notes['title'],
                    'note' => $notes['note'],
                    'created_by_user_id' => $event->userId(),
                ]);
            }
        }
    }

    protected function getUserType(User $user, array $data): UserTypesEnum
    {
        $user_type = array_key_exists('user_type', $data) ? $data['user_type'] : $user->user_type;
        $user_type = in_array($user_type, UserTypesEnum::cases()) ?
            $user_type : UserTypesEnum::LEAD;

        return $user_type;
    }

    protected function getDefaultTeamId(User $user, array $data, bool $is_updating): ?string
    {
        $default_team = array_key_exists('team_ids', $data) ? $data['team_ids'][0] : $data['team_id'] ?? null;

        if (! $is_updating && $default_team == null && ! $user->is_cape_and_bay_user) {
            $team_ids = TeamDetail::where('field', 'team-location')
                ->where('value', $user->home_location_id)->get()->pluck('team_id')->toArray();
            if (sizeof($team_ids) > 0) {
                $teams = Team::whereIn('id', $team_ids)->get()->pluck('id')->toArray();
                $default_team = $teams[array_rand($teams)];
            }
        }

        return $default_team;
    }
}
