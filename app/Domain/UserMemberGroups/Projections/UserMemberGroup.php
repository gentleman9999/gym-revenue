<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups\Projections;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Database\Factories\UserMemberGroupFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserMemberGroup extends GymRevProjection
{
    use HasFactory;

    /** @var array<string> */
    protected $fillable = ['client_id', 'member_group_id', 'user_id', 'is_primary'];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    /**
     * Create a new factory instance for the model.
     *
     */
    protected static function newFactory(): Factory
    {
        return UserMemberGroupFactory::new();
    }
}
