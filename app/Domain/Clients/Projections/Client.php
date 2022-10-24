<?php

namespace App\Domain\Clients\Projections;

use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Models\ClientSocialMedia;
use App\Domain\Conversations\Twilio\Actions\AddConversationAgent;
use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use App\Domain\Conversations\Twilio\Models\ClientConversation;
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
use Illuminate\Notifications\Notifiable;
use RuntimeException;
use Twilio\Exceptions\ConfigurationException;

/**
 * @property Collection       $gatewaySettings
 * @property string           $id
 * @property Collection<User> $users
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
     * Get all user who has AddConversationAgent::CHAT_CONVERSATION_ABILITY_NAME ability
     *
     * @return User|null
     */
    public function getNextFreeConversationAgent(): ?User
    {
        $users = $this->users()
            ->leftJoin('client_conversations', 'users.id', '=', 'client_conversations.user_id')
            ->oldest('client_conversations.updated_at')
            ->get();

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->can(AddConversationAgent::CHAT_CONVERSATION_ABILITY_NAME, ClientConversation::class)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @param array       $permission_names
     * @param string|null $entity_type
     *
     * @return Collection
     */
    public function getUsersWithPermissionQuery(array $permission_names, ?string $entity_type = null): Collection
    {
        return $this->users->filter(static fn (User $user) => $user->canAny($permission_names, $entity_type));
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
