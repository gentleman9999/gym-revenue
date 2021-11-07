<?php

namespace App\Models\Clients;

use App\Aggregates\Clients\ClientAggregate;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function details()
    {
        return $this->hasMany(ClientDetail::class);
    }

    public function detail()
    {
        return $this->hasOne(ClientDetail::class);
    }

    public function default_team_name()
    {
        return $this->detail()->where('detail', '=', 'default-team');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($client) {
            // @todo - make all this code be triggered by Projectors
            $default_team_name = $client->name . ' Home Office';
            $aggy = ClientAggregate::retrieve($client->id)
                ->createDefaultTeam($default_team_name)
                ->persist();
        });
    }

}
