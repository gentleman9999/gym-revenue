<?php

namespace App\Models\Clients\Features;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SmsCampaignDetails extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'client_id', 'sms_campaign_id', 'detail', 'value', 'misc', 'active'
    ];

    protected $casts = [
        'misc' => 'array'
    ];
}
