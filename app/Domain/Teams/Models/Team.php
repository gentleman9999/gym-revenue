<?php

declare(strict_types=1);

namespace App\Domain\Teams\Models;

use App\Domain\Clients\Projections\Client;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Team as JetstreamTeam;

/**
 *
 * @method static TeamFactory factory()
 */
class Team extends JetstreamTeam
{
    use HasFactory;
    use Sortable;
    use HasFactory;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $hidden = [
        'client_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'home_team' => 'boolean',
        'details' => 'array',
    ];

    /** @var array<string> */
    protected $fillable = [
        'name',
        'home_team',
    ];

    /** @var array<string, string> */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    public function locations(): array
    {
        return $this->details['team-locations'];
    }

    public function getTeamIDFromName(string $name): ?string
    {
        $record = $this->select('id')->where('name', '=', $name)->first();

        return $record->id ?? null;
    }

    public function team_users(): HasMany
    {
        return $this->hasMany(TeamUser::class, 'team_id', 'id')->with('user');
    }

    public function isClientsDefaultTeam(): bool
    {
        $proof = Client::where('details->default-team', $this->id)->first();

        return ($proof !== null);
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['club'] ?? null, function ($query, $location_id) {
            return $query->whereHas('detail', function ($query) use ($location_id) {
                return $query->whereField('team-location')->whereValue($location_id);
            });
        })->when($filters['users'] ?? null, function ($query, $user) {
            return $query->whereHas('team_users', function ($query) use ($user): void {
                $query->whereIn('user_id', $user);
            });
        });
    }

    /**
     * Get all of the users that belong to the team.
     *
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Jetstream::userModel(), Jetstream::membershipModel())
            ->with('roles')
            ->withTimestamps()
            ->as('membership');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the owner of the team. (Client)
     *
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get all of the team's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers(): Collection
    {
        return $this->users;
    }

    public function getGymRevTeamAttribute(): bool
    {
        return $this->client_id === null;
    }

    public static function fetchTeamIDFromName(string $name)
    {
        $model = new self();

        return $model->getTeamIDFromName($name);
    }

    public static function getTeamName($team_id): ?string
    {
        $model = self::select('name')->whereId($team_id)->first();

        return $model->name ?? null;
    }

    public static function getClientFromTeamId($team_id): ?string
    {
        $model = self::select('name')->whereId($team_id)->first();

        return $model->client_id ?? null;
    }

    public static function getGymRevAdminTeams(): Collection
    {
        return self::withoutGlobalScopes()->whereClientId(null)->get();
    }

    /**
     * The "booted" method of the model.
     *
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
        static::retrieved(function ($model): void {
            if ($model->client_id == null) {
                $model->setAppends(['GymRevTeam']);
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     */
    protected static function newFactory(): Factory
    {
        return TeamFactory::new();
    }
}
