<?php

namespace App\Domain\Clients\Projections;

use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Models\ClientSocialMedia;
use App\Domain\Conversations\Twilio\Actions\AddConversationAgent;
use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use App\Domain\LeadSources\LeadSource;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\MembershipType;
use App\Models\File;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GymRevProjection;
use App\Services\TwilioService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Twilio\Exceptions\ConfigurationException;

/**
 * @property Collection $gatewaySettings
 * @property string     $id
 */
class Client extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ClientDetail::class);
    }

    public function lead_types(): HasMany
    {
        return $this->hasMany(\App\Domain\LeadTypes\LeadType::class);
    }

    public function lead_sources(): HasMany
    {
        return $this->hasMany(LeadSource::class);
    }

    public function membership_types(): HasMany
    {
        return $this->hasMany(MembershipType::class);
    }

    public function trial_membership_types(): HasMany
    {
        return $this->hasMany(TrialMembershipType::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(ClientDetail::class);
    }

    public function home_team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function socialMedia(): HasMany
    {
        return $this->hasMany(ClientSocialMedia::class, );
    }

    public function accountOwners(): Collection
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('roles.scope', '=', $this->id)->whereGroup(SecurityGroupEnum::ACCOUNT_OWNER);
        })->orderBy('created_at')->get();
    }

    public function logo_url(): ?string
    {
        $file = File::whereType('logo')->first();

        return is_null($file) ? null : $file->url;
    }

    public function getSocialMedia(): Collection
    {
        return $this->socialMedia->keyBy('name')->map(fn ($s) => $s->value);
    }

    public function gatewaySettings(): HasMany
    {
        return $this->hasMany(ClientGatewaySetting::class);
    }

    public function getNamedGatewaySettings(): array
    {
        return $this->gatewaySettings->pluck('value', 'name')->toArray();
    }

    public function gatewayIntegration(): HasManyThrough
    {
        return $this->hasOneThrough(
            GatewayProvider::class,
            ClientGatewayIntegration::class,
            'client_id',
            'id',
            'id',
            'gateway_id',
        );
    }

    /**
     * @return TwilioService
     * @throws ConversationException
     * @throws ConfigurationException
     */
    public function getTwilioService(): TwilioService
    {
        return TwilioService::get($this);
    }

    /**
     * @return User|null
     */
    public function getNextFreeConversationAgent(): ?User
    {
        $permission_name = AddConversationAgent::CHAT_CONVERSATION_ABILITY_NAME;

        return User::join('abilities as a', 'a.name', '=', DB::raw("'{$permission_name}'"))
            ->join('permissions as p', static function (JoinClause $join) {
                $join->on('users.id', '=', 'p.entity_id')
                     ->on('a.id', '=', 'p.ability_id');
            })
            ->leftJoin('client_conversations as cc', 'users.id', '=', 'cc.user_id')
            ->select('users.*')
            ->where(['users.client_id' => $this->id])
            ->oldest('cc.updated_at')
            ->first();
    }

    public function getGatewayProviderBySlug(string $slug): GatewayProvider
    {
        return $this->gatewayIntegration()->where(['slug' => $slug])->first() ?: throw new RuntimeException(
            // IF YOU RUN INTO THIS ERROR: TRY RUNNING "artisan client:create-gateway"
            "No client integration configured for provider '{$slug}'"
        );
    }

    public function hasTwilioConversationEnabled(): bool
    {
        return $this->gatewayIntegration()->where(['slug' => GatewayProvider::PROVIDER_SLUG_TWILIO_CONVERSION])->exists();
    }
}
