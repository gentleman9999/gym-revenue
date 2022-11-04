<?php

namespace App\Domain\EndUsers\Leads\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;
use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\LeadTypes\LeadType;
use App\Models\Endusers\TrialMembership;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $primary_phone
 * @property Client  $client
 */
class Lead extends EndUser
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'lead_type_id',
        'lead_source_id',
        'lead_status_id',
    ];

    public static function getDetailsModel(): EndUserDetails
    {
        return new LeadDetails();
    }

    public function leadType(): HasOne
    {
        return $this->hasOne(LeadType::class, 'id', 'lead_type_id');
    }

    public function leadSource(): HasOne
    {
        return $this->hasOne(LeadSource::class, 'id', 'lead_source_id');
    }

    public function lead_status(): HasOne
    {
        return $this->hasOne(LeadStatus::class, 'id', 'lead_status_id');
    }

    public function trialMemberships(): HasMany
    {
        return $this->hasMany(TrialMembership::class)->orderBy('start_date', 'DESC');
    }

    public function scopeFilter($query, array $filters): void
    {
        parent::scopeFilter($query, $filters); // TODO: Change the autogenerated stub
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->orWhereHas('leadType', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when($filters['typeoflead'] ?? null, function ($query, $typeoflead) {
            $query->whereIn('lead_type_id',  $typeoflead);
            /* Filter for Location(s) */
        })->when($filters['grlocation'] ?? null, function ($query, $grlocation) {
            $query->whereIn('gr_location_id',  $grlocation);
            /* Filter for EndUser Sources */
        })->when($filters['leadsource'] ?? null, function ($query, $leadsource) {
            $query->whereIn('lead_source_id',  $leadsource);
            /* Filter for EndUser Sources */
        });
    }

    public function getPhoneNumber(): string
    {
        return $this->primary_phone;
    }

    public function isCBorGR(EndUser $user)
    {
        return (str_ends_with($user['email'], '@capeandbay.com') || str_ends_with($user['email'], '@gymrevenue.com'));
    }
}
