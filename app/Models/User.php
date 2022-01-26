<?php

namespace App\Models;

use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password'
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
//        return $this->details()->whereName('associated_client')->first();
    }

    public function isClientUser()
    {
        return !is_null($this->associated_client()->first());
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
        $current_team_id = $this->currentTeam()->first()->id;
        return $this->teams()->get()->keyBy('id')[$current_team_id]->pivot->role === 'Account Owner';
    }

    public function details()
    {
        return $this->hasMany('App\Models\UserDetails', 'user_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\UserDetails', 'user_id', 'id');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Models\Team', 'team_user', 'user_id', 'team_id')->withPivot('role');
    }

    public function default_team()
    {
        return $this->detail()->where('name', '=', 'default_team');
    }

    public function associated_client()
    {
        return $this->detail()->where('name', '=', 'associated_client');
    }

    public function phone()
    {
        return $this->detail()->where('name', '=', 'phone');
    }

    public function scopeFilter($query, array $filters)
    {
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
        });
    }
}
