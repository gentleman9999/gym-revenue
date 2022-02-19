<?php

namespace App\Models\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\LeadSource;
use App\Models\Endusers\LeadType;
use App\Models\Endusers\MembershipType;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


class Client extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function details()
    {
        return $this->hasMany(ClientDetail::class);
    }

    public function lead_types()
    {
        return $this->hasMany(LeadType::class);
    }

    public function lead_sources()
    {
        return $this->hasMany(LeadSource::class);
    }

    public function membership_types()
    {
        return $this->hasMany(MembershipType::class);
    }

    public function trial_membership_types()
    {
        return $this->hasMany(TrialMembershipType::class);
    }

    public function detail()
    {
        return $this->hasOne(ClientDetail::class);
    }

    public function default_team_name()
    {
        return $this->detail()->where('detail', '=', 'default-team');
    }

    public function teams()
    {
        return $this->details()->whereDetail('team')->whereActive(1);
    }

    public function services()
    {
        return $this->details()->whereDetail('service_slug')->whereActive(1);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($client) {
            // @todo - make all this code be triggered by Projectors
            $default_team_name = $client->name . ' Home Office';
            preg_match_all('/(?<=\s|^)[a-z]/i', $default_team_name, $matches);
            $prefix = strtoupper(implode('', $matches[0]));
            $aggy = ClientAggregate::retrieve($client->id)
                ->createDefaultTeam($default_team_name)
                ->createTeamPrefix((strlen($prefix) > 3) ? substr($prefix, 0, 3) : $prefix)
                ->createAudience("{$client->name} Prospects", 'prospects', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
                ->createAudience("{$client->name} Conversions", 'conversions', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
                ->createGatewayIntegration('sms', 'twilio', 'default_cnb', 'auto')
                ->createGatewayIntegration('email', 'mailgun', 'default_cnb', 'auto')
                    // @todo - add more onboarding shit here.
                ;
                $aggy->persist();
        });
    }

}
