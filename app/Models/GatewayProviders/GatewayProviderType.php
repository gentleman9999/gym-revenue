<?php

namespace App\Models\GatewayProviders;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class GatewayProviderType extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'desc', 'active', 'misc',
    ];

    protected $casts = [
        'misc' => 'array',
    ];

    public static function getAllTypesAsArray()
    {
        $results = [];

        $records = self::whereActive(1)->get();

        foreach ($records as $record) {
            $results[$record->name] = $record->toArray();
        }

        return $results;
    }
}
