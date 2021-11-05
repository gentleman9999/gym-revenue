<?php

namespace App\Models\Clients;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LocationDetails extends Model
{
    use SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['location_id', 'client_id',  'field', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
