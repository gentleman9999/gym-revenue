<?php

namespace App\Models\Clients\Features;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\Boolean;

class EmailCampaigns extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name', 'active', 'client_id', 'team_id', 'created_by_user_id'
    ];

    protected $casts = ['active'=> 'boolean'];

    public function details()
    {
        return $this->hasMany(EmailCampaignDetails::class, 'email_campaign_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne(EmailCampaignDetails::class, 'email_campaign_id', 'id');
    }

    public function assigned_template()
    {
        return $this->details()->whereDetail('template_assigned')->whereActive(1);
    }

    public function unassigned_template()
    {
        return $this->details()->whereDetail('template_assigned')->whereActive(0);
    }

    public function assigned_audience()
    {
        return $this->details()->whereDetail('audience_assigned')->whereActive(1);
    }

    public function unassigned_audience()
    {
        return $this->details()->whereDetail('audience_assigned')->whereActive(0);
    }

    public function schedule()
    {
        return $this->detail()->whereDetail('schedule')->whereActive(1);
    }

    public function schedule_date()
    {
        return $this->detail()->whereDetail('schedule_date')->whereActive(1);
    }

    protected static function booted()
    {
        static::created(function($campaign) {

            if(!is_null($campaign->client_id))
            {
                // use the client aggy to do a created details record
                ClientAggregate::retrieve($campaign->client_id)
                    ->createNewEmailCampaign($campaign->id,$campaign->created_by_user_id)
                    ->persist();
            }
            else
            {
                // make cnb created details record
                $detail = EmailCampaignDetails::create([
                    'email_campaign_id' => $campaign->id,
                    'detail' => 'created',
                    'value' => $campaign->created_by_user_id,
                ]);

                if($campaign->created_by_user_id == 'auto')
                {
                    $detail->misc = ['msg' => 'Template was auto-generated'];
                }
                else
                {
                    $user = User::find($campaign->created_by_user_id);
                    $detail->misc = ['msg' => 'Template was created by '.$user->name.' on '.date('Y-m-d')];
                }
            }
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('created_by_user_id', 'like', '%' . $search . '%')
                ;
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by_user_id');
    }

    public function launched(){
        return $this->detail()->whereDetail('launched');
    }
}
