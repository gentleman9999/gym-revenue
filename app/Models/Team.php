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

    public function client_details()
    {
        return $this->hasMany(ClientDetail::class, 'value',  'id')
            ->where('detail','=', 'team')
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
}
