<?php

namespace App\Domain\Users\Models;

use App\Domain\Clients\Projections\Client;
use App\Domain\Conversations\Twilio\Models\ClientConversation;
use App\Domain\Departments\Department;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Enums\SecurityGroupEnum;
use App\Enums\UserTypesEnum;
use App\Models\File;
use App\Models\Position;
use App\Models\Traits\Sortable;
use App\Scopes\ObfuscatedScope;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Silber\Bouncer\Database\Role;

/**
 * @property string                         $phone
 * @property string                         $client_id
 * @property int                            $id
 * @property string                         $last_name
 * @property string                         $first_name
 * @property string                         $name       Full name
 * @property Client                         $client
 * @property Collection<ClientConversation> $twilioClientConversation
 *
 * @method static UserFactory factory()
 */
class User extends Authenticatable
{
    use HasTeams;
    use HasFactory;
    use Notifiable;
    use Impersonate;
    use HasApiTokens;
    use HasProfilePhoto;
    use HasRolesAndAbilities;
    use TwoFactorAuthenticatable;
    use Sortable;
    use SoftDeletes;

    /**
     * Define the table name so that all
     * children model uses this table
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'phone', 'alternate_phone',
        'date_of_birth', 'gender', 'occupation', 'employer', 'address1', 'address2',
        'zip', 'city', 'state', 'drivers_license_number', 'unsubscribed_email',
        'unsubscribed_sms', 'obfuscated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'access_token',
        'client_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'is_cape_and_bay_user' => 'boolean',
        'unsubscribed_email' => 'boolean',
        'unsubscribed_sms' => 'boolean',
        'is_previous' => 'boolean',

        'alternate_emails' => 'array',
        'entry_source' => 'array',
        'misc' => 'array',

        'email_verified_at' => 'datetime',
        'date_of_birth' => 'datetime',
        'terminated_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'user_type' => UserTypesEnum::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'name',
    ];

    /**
     * Override soft detele column name
     */
    public const DELETED_AT = 'terminated_at';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ObfuscatedScope());
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    /**
     * Determine if the user belongs to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function belongsToTeam($team): bool
    {
        if (is_null($team)) {
            return false;
        }

//        if($this->ownsTeam($team)){
//            return true;
//        }
        return $this->teams->contains(function ($t) use ($team) {
            return $t->id === $team->id;
        });
    }

    public function allLocations(): Collection
    {
        return Location::get();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isClientUser(): bool
    {
        return $this->client_id !== null;
    }

    /**
     * If user is an AccountOwner of the currentClient
     * @return bool
     */
    public function isAccountOwner(): bool
    {
        return $this->inSecurityGroup(SecurityGroupEnum::ACCOUNT_OWNER);
    }

    /**
     * If user is an AccountOwner of the currentClient
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->inSecurityGroup(SecurityGroupEnum::ADMIN);
    }

    public function details(): HasMany
    {
        return $this->hasMany('App\Domain\Users\Models\UserDetails', 'user_id', 'id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne('App\Domain\Users\Models\UserDetails', 'user_id', 'id');
    }

    public function twilioClientConversation(): HasMany
    {
        return $this->hasMany(ClientConversation::class, 'user_id', 'id');
    }

    public function contact_preference(): HasOne
    {
        return $this->detail()->where('field', '=', 'contact_preference');
    }

    public function emergencyContact(): HasOne
    {
        return $this->detail()->where('field', '=', 'emergency_contact');
    }

    public function notes(): HasMany
    {
        return $this->hasMany('App\Models\Note', 'entity_id')->whereEntityType(self::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
    }

    public function defaultTeamDetail(): HasOne
    {
        return $this->detail()->where('field', '=', 'default_team_id');
    }

    public function defaultTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', $this->default_team_id);
    }

    public function getDefaultTeam(): Team
    {
        return Team::find($this->default_team_id);
    }

    public function api_token(): HasOne
    {
        return $this->detail()->where('field', '=', 'api-token');
    }

    public function column_config(): HasMany
    {
        return $this->details()->where('field', '=', 'column-config');
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('address1', 'like', '%' . $search . '%')
                    ->orWhere('address2', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('zip', 'like', '%' . $search . '%');
            });
        })->when($filters['club'] ?? null, function ($query, $location_id) {
            /*$query->whereHas('teams', function ($query) use ($location_id) {
                return $query->whereHas('detail', function ($query) use ($location_id) {
                    return $query->whereName('team-location')->whereValue($location_id);
                });
            });*/
            //This returns home club location instead of the above clubs a user is a part of.
            $query->where(function ($query) use ($location_id) {
                $query->where('home_location_id', '=', $location_id);
            });
        })->when($filters['team'] ?? null, function ($query, $team_id) {
            $query->whereHas('teams', function ($query) use ($team_id) {
                return $query->whereTeamId($team_id);
            });
        })->when($filters['roles'] ?? null, function ($query, $role) {
            $query->join('assigned_roles', function ($join) use ($role) {
                $join->on('users.id', '=', 'assigned_roles.entity_id')
                    ->where('assigned_roles.role_id', '=', $role);
            })->get();
        });
    }

    public function role(): ?Role
    {
        return $this->roles[0] ?? null;
    }

    public function getRole(): Role | string | null
    {
        return $this->getRoles()[0] ?? null;
//        if(!$roles || !count($roles)){
//            return null;
//        }
//        return $roles[0];
    }

    public function securityGroup(): ?SecurityGroupEnum
    {
        $role = $this->role();
        if (! $role) {
            return null;
        }

        return SecurityGroupEnum::from($role->group);
    }

    public function inSecurityGroup(SecurityGroupEnum ...$groups): bool
    {
        return in_array($this->securityGroup(), $groups);
    }

    public function isAtLeastSecurityGroup(SecurityGroupEnum $group): bool
    {
        return $this->securityGroup()->value <= $group->value;
    }

    public function notifications(): HasMany
    {
        return $this->hasMany('notifications');
    }

    public function home_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'home_location_id', 'gymrevenue_id');
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getIsManagerAttribute()
    {
        return $this->manager !== null && $this->manager !== '';
    }

    public function getDefaultTeamIdAttribute(): ?string
    {
        if ($this->defaultTeamDetail) {
            return $this->defaultTeamDetail->value;
        }

        return null;
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_department', 'user_id', 'department_id');
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'user_position', 'user_id', 'position_id');
    }

    /**
     * Get all of the teams the user owns or belongs to.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allTeams(): Collection
    {
        return $this->teams->sortBy('name');
    }

    /**
     * Checks if instance is of an enduser
     *
     * @return bool
     */
    public function isEndUser(): bool
    {
        return in_array(
            $this->user_type,
            [UserTypesEnum::LEAD, UserTypesEnum::CUSTOMER, UserTypesEnum::MEMBER]
        );
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public static function withTerminated(): Builder
    {
        return self::withTrashed();
    }

    public static function onlyTerminated(): Builder
    {
        return self::onlyTrashed();
    }

    public static function withoutTerminated(): Builder
    {
        return self::withoutTrashed();
    }

    public function terminate(): void
    {
        $this->delete();
    }

    public function reinstate(): void
    {
        $this->restore();
    }

    public function getDeletedAtColumn(): string
    {
        return 'terminated_at';
    }
}
