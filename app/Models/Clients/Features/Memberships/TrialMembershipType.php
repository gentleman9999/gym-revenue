<?php

declare(strict_types=1);

namespace App\Models\Clients\Features\Memberships;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrialMembershipType extends Model
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
        'id',
        'client_id',
        'type_name',
        'slug',
        'trial_length',
        'locations',
        'misc',
        'active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'locations' => 'array',
    ];
}
