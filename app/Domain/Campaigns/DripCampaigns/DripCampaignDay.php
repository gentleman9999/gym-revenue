<?php

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DripCampaignDay extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    public mixed $dayOfCampaign;
    protected $hidden = ['client_id'];

    protected $fillable = ['drip_campaign_id', 'day_of_campaign', 'email_template_id', 'sms_template_id', 'call_template_id'];

    public function dripCampaignDay(): BelongsTo
    {
        return $this->belongsTo(DripCampaign::class);
    }

    public function emailTemplate(): HasOne
    {
        return $this->hasOne(EmailTemplate::class);
    }

    public function smsTemplate(): HasOne
    {
        return $this->hasOne(SmsTemplate::class);
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
