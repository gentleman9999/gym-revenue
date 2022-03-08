<?php

namespace App\Models;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'first_name', 'last_name'
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
        return !is_null($this->associated_client()->first());
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
        return $current_team ?  $current_team->pivot->role === 'Account Owner' : false;
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

    public function phone()
    {
        return $this->detail()->where('name', '=', 'phone');
    }

    public function phone_number()
    {
        return $this->detail()->where('name', '=', 'phone');
    }

    public function altEmail()
    {
        return $this->detail()->where('name', '=', 'altEmail');
    }

    public function address1()
    {
        return $this->detail()->where('name', '=', 'address1');
    }

    public function address2()
    {
        return $this->detail()->where('name', '=', 'address2');
    }

    public function city()
    {
        return $this->detail()->where('name', '=', 'city');
    }
    public function state()
    {
        return $this->detail()->where('name', '=', 'state');
    }

    public function zip()
    {
        return $this->detail()->where('name', '=', 'zip');
    }

    public function jobTitle()
    {
        return $this->detail()->where('name', '=', 'jobTitle');
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
        return $this->belongsToMany('App\Models\Team', 'team_user', 'user_id', 'team_id')->withPivot('role');
    }
    public function potentialRoles()
    {
        return $this->belongsToMany('App\Models\Team', 'team_user', 'user_id', 'team_id')->withPivot('role');
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

    public function security_role()
    {
        return $this->detail()->where('name', '=', 'security_role');
    }

    public function is_manager()
    {
        return $this->detail()->where('name', '=', 'is_manager');
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
             $query->whereHas('potentialRoles', function ($query) use ($role) {
                $query->where('role', '=', $role );

            });
        })
        ;
    }
}
