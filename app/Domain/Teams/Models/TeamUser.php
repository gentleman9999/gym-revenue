<?php

declare(strict_types=1);

namespace App\Domain\Teams\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;

class TeamUser extends Model
{
    use HasFactory;

    /** @var string */
    protected $table = 'team_user';

    /** @var array<string, string> */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamDeleted::class,
        'deleted' => TeamUpdated::class,
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
