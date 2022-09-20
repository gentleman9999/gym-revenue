<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $payload): ClientVoiceCallLog
 */
class ClientConversation extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'client_id',
        'user_id',
        'conversation_id',
        'gateway_provider_id',
        'user_conversation_id',
    ];
}
