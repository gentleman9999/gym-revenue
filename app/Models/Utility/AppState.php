<?php

declare(strict_types=1);

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AppState extends Model
{
    use Notifiable;
    use SoftDeletes;
    use HasUuids;

    public int $name = 22;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = [
        'name',
        'slug',
        'desc',
        'value',
        'misc',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'misc' => 'array',
    ];

    public static function isSimuationMode(): bool
    {
        $results = true;

        $record = self::whereSlug('is-simulation-mode')->first();

        if ($record !== null) {
            $results = ($record->value == '1');
        }

        return $results;
    }
}
