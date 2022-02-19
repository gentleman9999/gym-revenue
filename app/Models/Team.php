<?php

namespace App\Models;

use App\Models\Clients\ClientDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;

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
        'user_id',
        'name',
        'personal_team',
        'default_team',
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

    public function client_details()
    {
        return $this->hasMany(ClientDetail::class, 'value',  'id')
            ->where('detail','=', 'team')
            ->with('client');
    }

    public function locations()
    {
        return $this->details()->whereName('team-location');
    }

    public function default_team_details()
    {
        return $this->hasOne(ClientDetail::class, 'value',  'id')
            ->where('detail','=', 'default-team')
            ->with('client');
    }

    public static function fetchTeamIDFromName(string $name)
    {
        $model = new self;

        return $model->getTeamIDFromName($name);
    }

    public function getTeamIDFromName(string $name)
    {
        $results = false;

        $record = $this->select('id')->where('name', '=', $name)->first();

        if(!is_null($record))
        {
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

        //dd('Bitch', $proof);

        return (!is_null($proof));
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
