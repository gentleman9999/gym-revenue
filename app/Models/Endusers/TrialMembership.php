<?php

declare(strict_types=1);

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrialMembership extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = [
        'client_id',
        'type_id',
        'lead_id',
        'start_date',
        'expiry_date',
        'location_id',
        'active',
    ];
}
