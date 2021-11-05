<?php

namespace App\Models\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Team;
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

    public function details()
    {
        return $this->hasMany('App\Models\Clients\ClientDetail', 'client_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\Clients\ClientDetail', 'client_id', 'id');
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
            $default_team_name = $client->name.' Home Office';
            $aggy = ClientAggregate::retrieve($client->id)
                ->createDefaultTeam($default_team_name)
                ->persist();
        });
    }

}
