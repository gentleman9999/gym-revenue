<?php

namespace App\Models\Comms;

use App\Models\Clients\Features\EmailCampaigns;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;

class QueuedEmailCampaign extends Model
{
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'email_campaign_id', 'trigger_at',  'started_at', 'completed_at',
    ];

    public function campaign()
    {
        return $this->hasOne(EmailCampaigns::class, 'id', 'email_campaign_id');
    }
}
