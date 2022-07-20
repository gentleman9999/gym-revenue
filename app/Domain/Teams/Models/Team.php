<?php

namespace App\Domain\Teams\Models;

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientDetail;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;
    use Sortable;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = [
        'client_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'home_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'home_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
        static::retrieved(function ($model) {
            if ($model->client_id == null) {
                $model->setAppends(['GymRevTeam']);
            }
        });
    }

    public function details()
    {
        return $this->hasMany('App\Domain\Teams\Models\TeamDetail', 'team_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Domain\Teams\Models\TeamDetail', 'team_id', 'id');
    }

    public function locations()
    {
        return $this->details()->whereName('team-location');
    }

    public function default_team_details()
    {
        return $this->hasOne(ClientDetail::class, 'value', 'id')
            ->where('detail', '=', 'default-team')
            ->with('client');
    }

    public static function fetchTeamIDFromName(string $name)
    {
        $model = new self();

        return $model->getTeamIDFromName($name);
    }

    public function getTeamIDFromName(string $name)
    {
        $results = false;

        $record = $this->select('id')->where('name', '=', $name)->first();

        if ($record != null) {
            $results = $record->id;
        }

        return $results;
    }

    public function team_users()
    {
        return $this->hasMany(TeamUser::class, 'team_id', 'id')
            ->with('user');
    }

    public function isClientsDefaultTeam()
    {
        $proof = $this->default_team_details()->first();

        return (! is_null($proof));
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['club'] ?? null, function ($query, $location_id) {
            return $query->whereHas('detail', function ($query) use ($location_id) {
                return $query->whereName('team-location')->whereValue($location_id);
            });
        })->when($filters['users'] ?? null, function ($query, $user) {
            return $query->whereHas('team_users', function ($query) use ($user) {
                $query->whereIn('user_id', $user);
            });
        });
    }

    public static function getTeamName($team_id)
    {
        $results = 'No Name';

        $model = self::select('name')->whereId($team_id)->first();

        if ($model !== null) {
            $results = $model->name;
        }

        return $results;
    }

    public static function getClientFromTeamId($team_id)
    {
        $results = null;

        $model = self::select('name')->whereId($team_id)->first();

        if ($model != null) {
            $results = $model->client_id;
        }

        return $results;
    }

    /**
     * Get all of the users that belong to the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Jetstream::userModel(), Jetstream::membershipModel())
//            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public static function getGymRevAdminTeams()
    {
        return self::withoutGlobalScopes()->whereClientId(null)->get();
    }

    /**
     * Get the owner of the team. (Client)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**

     * Get all of the team's users including its owner.

     *

     * @return \Illuminate\Support\Collection

     */
    public function allUsers()
    {
        return $this->users;
    }

    public function getGymRevTeamAttribute()
    {
        return $this->client_id === null;
    }
}