<?php

namespace App\Domain\EndUsers\Leads\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;
use App\Domain\Teams\Models\Team;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function getInteractionCount()
    {
        $aggy = EndUserAggregate::retrieve($this->id);

        return $aggy->getInteractionCount();
    }

    public function getTeamUsers()
    {
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $current_team = Team::find($session_team['id']);
        } else {
            $current_team = Team::find($this->default_team_id);
        }
        $team_users = $current_team->team_users()->get();

        return $team_users;
    }
}
