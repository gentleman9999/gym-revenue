<?php

namespace App\Domain\Clients\Projections;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Audiences\Audience;
use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Models\ClientSocialMedia;
use App\Domain\Conversations\Twilio\Actions\AddConversationAgent;
use App\Domain\Conversations\Twilio\Models\ClientConversation;
use App\Domain\Departments\Department;
use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadTypes\LeadType;
use App\Domain\Locations\Projections\Location;
use App\Domain\Roles\Role;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\Customer;
use App\Domain\Users\Models\Employee;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use App\Enums\UserTypesEnum;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\MembershipType;
use App\Models\File;
use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GymRevProjection;
use App\Services\MailgunService;
use App\Services\TwilioService;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use RuntimeException;

/**
 * @property Collection       $gatewaySettings
 * @property string           $id
 * @property Collection<User> $users
 * @method static ClientFactory factory()
 */
class Client extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'details',
    ];

    protected $casts = [
        'services' => 'array',
        'details' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return ClientFactory::new();
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function lead_types(): HasMany
    {
        return $this->hasMany(LeadType::class);
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

    public function home_team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function allUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function users(): HasMany
    {
        return $this->allUsers()->whereUserType(UserTypesEnum::EMPLOYEE);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'scope');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
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

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function agreementCategories(): HasMany
    {
        return $this->hasMany(AgreementCategory::class);
    }

    public function agreementTemplates(): HasMany
    {
        return $this->hasMany(AgreementTemplate::class);
    }

    public function audiences(): HasMany
    {
        return $this->hasMany(Audience::class);
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
     */
    public function getTwilioService(): TwilioService
    {
        return TwilioService::get($this);
    }

    /**
     * @return MailgunService
     */
    public function getMailGunService(): MailgunService
    {
        return MailgunService::get($this);
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
        // IF YOU RUN INTO THIS ERROR: TRY RUNNING "artisan client:create-gateway"
        return $this->gatewayIntegration()->where(['slug' => $slug])->first() ?: throw new RuntimeException(
            "No client integration configured for provider '{$slug}'"
        );
    }

    public function hasTwilioConversationEnabled(): bool
    {
        return $this->gatewayIntegration()->where(['slug' => GatewayProvider::PROVIDER_SLUG_TWILIO_CONVERSION])->exists();
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function getDetailsAttribute(): array
    {
        return $this->details ?? [];
    }
}
