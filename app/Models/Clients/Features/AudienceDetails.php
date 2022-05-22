<?php

namespace App\Models\Clients\Features;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AudienceDetails extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'detail', 'value', 'misc', 'active', 'client_id', 'audience_id',
    ];

    protected $casts = [
        'misc' => 'array',
    ];
}
