<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamDetail extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['team_id', 'name', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array',
    ];

    public function team()
    {
        return $this->belongsTo('App\Domain\Teams\Models\Team', 'team_id', 'id');
    }
}
