<?php

namespace App\Domain\Audiences;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audience extends GymRevProjection
{
    use SoftDeletes;

    protected $hidden = ['client_id'];

    protected $fillable = ['name', 'entity', 'filters'];

    protected $casts = [
        'filters' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getTextable(): Collection
    {
        return $this->getEntityBaseQuery()->whereUnsubscribedSms(0)->get();
    }

    public function getEmailable(): Collection
    {
        return $this->getEntityBaseQuery()->whereUnsubscribedEmail(0)->get();
    }

    protected function getEntityBaseQuery(): Builder
    {
        $entity = new $this->entity();

        return $entity::withoutGlobalScopes()
            ->whereClientId($this->client_id)
            ->whereNull('member_id')
            ->when($this->filters !== null, function ($query) {
                return $this->generateQueryFromFilters($query);
            });
    }

    protected function generateQueryFromFilters($query)
    {
        //TODO: this should convert the json filter structure into query builder.
        return $query;
    }
}
