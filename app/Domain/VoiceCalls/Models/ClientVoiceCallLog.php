<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Models;

use App\Domain\VoiceCalls\Events\ClientVoiceCallLogCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $payload): ClientVoiceCallLog
 */
class ClientVoiceCallLog extends Model
{
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'payload' => 'json',
    ];

    /** @var array<string> */
    protected $fillable = [
        'gateway_id',
        'user_id',
        'to',
        'conversation_id',
        'status',
        'payload',
    ];

    public static function add(array $attributes): void
    {
        event(new ClientVoiceCallLogCreated($attributes));
    }
}
