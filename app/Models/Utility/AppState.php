<?php

namespace App\Models\Utility;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AppState extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'desc', 'value', 'misc'
    ];

    protected $casts = [
        'misc' => 'array'
    ];

    public static function isSimuationMode()
    {
        $results = true;

        $record = self::whereSlug('is-simulation-mode')->first();

        if(!is_null($record))
        {
            $results = ($record->value == '1');
        }

        return $results;
    }
}
