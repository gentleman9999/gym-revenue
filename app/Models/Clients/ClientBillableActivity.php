<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientBillableActivity extends Model
{
    use SoftDeletes;

    /** @var array<string> */
    protected $fillable = [
        'client_id',
        'desc',
        'entity_type',
        'entity_id',
        'units',
        'misc',
        'triggered_by_user_id',
    ];
}
