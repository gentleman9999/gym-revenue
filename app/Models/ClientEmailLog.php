<?php

namespace App\Models;

use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ClientEmailLog extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['id','client_id','gateway_id','message_id','campaign_id','email_template_id',
        'recipient_type','recipient_id','recipient_email','initiated_at','accepted_at','sent_at',
        'delivered_at','failed_at',];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
