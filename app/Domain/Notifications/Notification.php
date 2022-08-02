<?php

namespace App\Domain\Notifications;

use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['state', 'text', 'misc', 'entity_id', 'entity_type', 'entity'];

    protected $casts = [
        'misc' => 'array',
        'entity' => 'array',
    ];

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
