<?php

namespace App\Models;

use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Traits\Sortable;
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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
        'default_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'personal_team',
        'default_team',
        'client_id',
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

    public function details()
    {
        return $this->hasMany('App\Models\TeamDetail', 'team_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\TeamDetail', 'team_id', 'id');
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

        if (! is_null($record)) {
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
        })->when($filters['club'] ?? null, function ($query, $club_id) {
            return $query->whereHas('detail', function ($query) use ($club_id) {
                return $query->whereName('team-location')->whereValue($club_id);
            });
        })->when($filters['users'] ?? null, function ($query, $user) {
            return $query->whereHas('team_users', function ($query) use ($user) {
                $query->whereIn('user_id',  $user);
            });
        });
    }

    public static function getTeamName($team_id)
    {
        $results = 'No Name';

        $model = self::select('name')->whereId($team_id)->first();

        if (! is_null($model)) {
            $results = $model->name;
        }

        return $results;
    }

    public static function getClientFromTeamId($team_id)
    {
        $results = null;

        $model = self::select('name')->whereId($team_id)->first();

        if (! is_null($model)) {
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
}
