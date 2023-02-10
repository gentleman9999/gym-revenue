<?php

declare(strict_types=1);

namespace App\Domain\Clients\Models;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientGatewaySetting extends GymRevProjection
{
    use HasFactory;

    public const NAME_CLIENT_ID                       = 'client_id';
    public const NAME_MAILGUN_DOMAIN                  = 'mailgunDomain';
    public const NAME_MAILGUN_SECRET                  = 'mailgunSecret';
    public const NAME_MAILGUN_FROM_ADDRESS            = 'mailgunFromAddress';
    public const NAME_MAILGUN_FROM_NAME               = 'mailgunFromName';
    public const NAME_TWILIO_SID                      = 'twilioSID';
    public const NAME_TWILIO_TOKEN                    = 'twilioToken';
    public const NAME_TWILIO_NUMBER                   = 'twilioNumber';
    public const NAME_TWILIO_API_KEY                  = 'twilioApiKey';
    public const NAME_TWILIO_API_SECRET               = 'twilioApiSecret';
    public const NAME_TWILIO_MESSENGER_ID             = 'twilioMessengerID';
    public const NAME_TWILIO_CONVERSATION_SERVICES_ID = 'twilioConversationServiceSID';

    /** @var array<string>  */
    protected $fillable = [
        'gateway_provider',
        'name',
        'value',
        'client_id',
    ];

    /** @var array<string>  */
    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
