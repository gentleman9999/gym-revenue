<?php

namespace App\Domain\Teams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;

class TeamUser extends Model
{
    use HasFactory;

    protected $table = 'team_user';

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamDeleted::class,
        'deleted' => TeamUpdated::class,
    ];

    public function teams()
    {
        return $this->hasMany('App\Domain\Teams\Models\Team', 'team_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Domain\Users\Models\User::class, 'user_id', 'id');
    }
}
