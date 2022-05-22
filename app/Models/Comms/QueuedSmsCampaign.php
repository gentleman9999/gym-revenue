<?php

namespace App\Models\Comms;

use App\Models\Clients\Features\SmsCampaigns;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;

class QueuedSmsCampaign extends Model
{
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'sms_campaign_id', 'trigger_at', 'started_at', 'completed_at',
    ];

    public function campaign()
    {
        return $this->hasOne(SmsCampaigns::class, 'id', 'sms_campaign_id');
    }
}
