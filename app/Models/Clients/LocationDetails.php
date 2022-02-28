<?php

namespace App\Models\Clients;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationDetails extends Model
{
    use SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['location_id', 'client_id', 'field', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public static function createOrUpdateRecord($location_id, $client_id, $field, $value)
    {
        $model = self::firstOrCreate([
            'location_id' => $location_id,
            'client_id' => $client_id,
            'field' => $field
        ]);

        $model->value = $value;
        $model->save();
    }
}
