<?php

declare(strict_types=1);

namespace App\Domain\Notifications;

use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property int    $user_id
 */
class Notification extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    /** @see resources/js/utils/parseNotificationResponse::NOTIFICATION_TYPES */
    public const TYPE_CALENDAR_EVENT_REMINDER = 'CALENDAR_EVENT_REMINDER';
    public const TYPE_NEW_CONVERSATION = 'NEW_CONVERSATION';
    public const TYPE_DEFAULT = 'DEFAULT_NOTIFICATION';

    public const STATE_INFO = 'info';
    public const STATE_DEFAULT = 'default';
    public const STATE_SUCCESS = 'success';
    public const STATE_WARNING = 'warning';
    public const STATE_ERROR = 'error';

    protected $fillable = [
        'state',
        'text',
        'misc',
        'entity_id',
        'entity_type',
        'entity',
    ];

    protected $casts = [
        'misc' => 'array',
        'entity' => 'array',
    ];

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
