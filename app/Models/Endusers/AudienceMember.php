<?php

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudienceMember extends Model
{
    use hasFactory, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'client_id', 'audience_id', 'entity_id', 'entity_type', 'subscribed', 'misc', 'dnc'
    ];

    protected $casts = [
        'misc' => 'array'
    ];
}
