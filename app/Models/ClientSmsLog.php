<?php

declare(strict_types=1);

namespace App\Models;

use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ClientSmsLog extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;

    /** @var array<string> */
    protected $fillable = [
        'id',
        'client_id',
        'gateway_id',
        'message_id',
        'campaign_id',
        'sms_template_id',
        'recipient_type',
        'recipient_id',
        'recipient_phone',
        'initiated_at',
        'accepted_at',
        'sent_at',
        'delivered_at',
        'failed_at',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
