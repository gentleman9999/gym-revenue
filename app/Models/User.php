<?php

namespace App\Models;

use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable
{
    use HasTeams;
    use HasFactory;
    use Notifiable;
    use Impersonate;
    use HasApiTokens;
    use HasProfilePhoto;
    use HasRolesAndAbilities;
    use TwoFactorAuthenticatable;
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'first_name', 'last_name', 'address1', 'address2', 'city', 'state', 'zip', 'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the current team of the user's context.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentTeam()
    {
        if (is_null($this->current_team_id) && $this->id) {
            $default_team = $this->default_team()->first();
            $team_record = Team::find($default_team->value);
            $this->switchTeam($team_record);
            //$this->switchTeam($this->personalTeam());
        }

        return $this->belongsTo(Jetstream::teamModel(), 'current_team_id');
    }

    public function allLocations()
    {
        return Location::whereClientId($this->currentClientId())->get();
    }

    public function switchLocation($location)
    {
        $this->current_location_id = $location->id;

        return $this->save();
    }

    public function currentClientId()
    {
        $detail = ClientDetail::whereDetail('team')->whereValue($this->current_team_id)->first();

        return is_null($detail) ? null : $detail->client_id;
    }

    public function isClientUser()
    {
        return ! is_null($this->associated_client()->first());
    }

    public function client()
    {
        $associated_client = $this->associated_client()->first();

        return Client::find($associated_client);
    }

    public function isCapeAndBayUser()
    {
        return $this->teams()->get()->contains('id', 1);//ID1 = CapeAndBayAdminTeam
    }

    /**
     * If user is AccountOwner of the currentTeam
     * @return bool
     */
    public function isAccountOwner()
    {
        $current_team_id = $this->currentTeam()->first()->id ?? null;
        $current_team = $this->teams()->get()->keyBy('id')[$current_team_id] ?? null;

        return $current_team ? $current_team->pivot->role === 'Account Owner' : false;
    }

    public function details()
    {
        return $this->hasMany('App\Models\UserDetails', 'user_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\UserDetails', 'user_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'user_id', 'id');
    }

    public function altEmail()
    {
        return $this->detail()->where('name', '=', 'altEmail');
    }

    public function jobTitle()
    {
        return $this->detail()->where('name', '=', 'jobTitle');
    }

    public function contact_preference()
    {
        return $this->detail()->where('name', '=', 'contact_preference');
    }

    public function notes()
    {
        return $this->hasMany('App\Models\Note', 'entity_id')->whereEntityType(self::class);
    }

    public function start_date()
    {
        return $this->detail()->where('name', '=', 'start_date');
    }

    public function end_date()
    {
        return $this->detail()->where('name', '=', 'end_date');
    }

    public function termination_date()
    {
        return $this->detail()->where('name', '=', 'termination_date');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Models\Team', 'team_user', 'user_id', 'team_id');
    }

    public function default_team()
    {
        return $this->detail()->where('name', '=', 'default_team');
    }

    public function home_club()
    {
        return $this->detail()->where('name', '=', 'home_club');
    }

    public function associated_client()
    {
        return $this->detail()->where('name', '=', 'associated_client');
    }

    public function classification()
    {
        return $this->detail()->where('name', '=', 'classification');
    }

    public function is_manager()
    {
        return $this->detail()->where('name', '=', 'is_manager');
    }

    public function api_token()
    {
        return $this->detail()->where('name', '=', 'api-token');
    }

    public function column_config()
    {
        return $this->details()->where('name', '=', 'column-config');
    }

    public function scopeFilter($query, array $filters)
    {
        $stop = 0;
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['club'] ?? null, function ($query, $club_id) {
            $query->whereHas('teams', function ($query) use ($club_id) {
                return $query->whereHas('detail', function ($query) use ($club_id) {
                    return $query->whereName('team-location')->whereValue($club_id);
                });
            });
        })->when($filters['team'] ?? null, function ($query, $team_id) {
            $query->whereHas('teams', function ($query) use ($team_id) {
                return $query->whereTeamId($team_id);
            });
        })->when($filters['roles'] ?? null, function ($query, $role) {
            $query->join('assigned_roles', function ($join) use ($role) {
                $join->on('users.id', '=', 'assigned_roles.entity_id')
                    ->where('assigned_roles.role_id', '=', $role);
            })->get();
        });
    }

    public function role()
    {
        return $this->roles[0] ?? null;
    }

    public function getRole()
    {
        return $this->getRoles()[0] ?? null;
//        if(!$roles || !count($roles)){
//            return null;
//        }
//        return $roles[0];
    }

    public function securityGroup()
    {
        $role = $this->role();
        if (! $role) {
            return null;
        }

        return SecurityGroupEnum::from($role->group);
    }

    public function inSecurityGroup(SecurityGroupEnum ...$groups)
    {
        return in_array($this->securityGroup(), $groups);
    }

    public function isAtLeastSecurityGroup(SecurityGroupEnum $group)
    {
        return $this->securityGroup()->value <= $group->value;
    }

    public function notifications()
    {
        return $this->hasMany('notifications');
    }
}
