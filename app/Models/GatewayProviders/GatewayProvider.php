<?php

namespace App\Models\GatewayProviders;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class GatewayProvider extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'desc', 'vendor', 'provider_type', 'provider_rate', 'gr_commission_rate', 'gr_commission_bulk_rate','active', 'misc'
    ];

    protected $casts = [
        'misc' => 'array'
    ];

    public static function getAllProvidersAsArray()
    {
        $results = [];

        $records = self::whereActive(1)->get();

        foreach($records as $record)
        {
            $results[$record->slug] = $record->toArray();
        }

        return $results;
    }
}