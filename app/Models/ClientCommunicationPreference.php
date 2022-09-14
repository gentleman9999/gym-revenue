<?php

declare(strict_types=1);

namespace App\Models;

use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $client_id
 * @property bool   $voice
 * @property bool   $sms
 * @property bool   $email
 * @property bool   $conversation
 */
class ClientCommunicationPreference extends GymRevProjection
{
    public const COMMUNICATION_TYPES_VOICE        = 'voice';
    public const COMMUNICATION_TYPES_SMS          = 'sms';
    public const COMMUNICATION_TYPES_EMAIL        = 'email';
    public const COMMUNICATION_TYPES_CONVERSATION = 'conversation';

    public const COMMUNICATION_TYPES = [
        self::COMMUNICATION_TYPES_VOICE        => 'Voice',
        self::COMMUNICATION_TYPES_EMAIL        => 'Email',
        self::COMMUNICATION_TYPES_SMS          => 'SMS',
        self::COMMUNICATION_TYPES_CONVERSATION => 'Conversation',
    ];

    use HasFactory;

    protected $casts = [
        self::COMMUNICATION_TYPES_VOICE        => 'bool',
        self::COMMUNICATION_TYPES_SMS          => 'bool',
        self::COMMUNICATION_TYPES_EMAIL        => 'bool',
        self::COMMUNICATION_TYPES_CONVERSATION => 'bool',
    ];

    protected $fillable = [
        self::COMMUNICATION_TYPES_VOICE,
        self::COMMUNICATION_TYPES_SMS,
        self::COMMUNICATION_TYPES_EMAIL,
        self::COMMUNICATION_TYPES_CONVERSATION,
    ];

    protected $hidden = [
        'client_id'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
