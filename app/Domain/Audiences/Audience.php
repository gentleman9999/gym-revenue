<?php

namespace App\Domain\Audiences;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audience extends GymRevProjection
{
    use SoftDeletes;

    protected $hidden = ['client_id'];

    protected $fillable = ['name', 'entity', 'filters', 'editable'];

    protected $casts = [
        'filters' => 'array',
    ];

    protected static function booted(): void
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

        $query = $entity::withoutGlobalScopes()
            ->whereClientId($this->client_id)
            ->when($this->filters !== null, function ($query) {
                $this->generateQueryFromFilters($query);
            });

        if ($entity instanceof Lead) {
            $query->whereNull('member_id');
        }

        return $query;
    }

    protected function generateQueryFromFilters($query)
    {
        $entity = (new ($this->entity));
        $fillable = collect($entity->getFillable());
//        dd($fillable);
        foreach (array_keys($this->filters) as $filter) {
            if (collect($entity->getFillable())->contains($filter)) {
//                dd($this->filters[$filter]);
                $query->whereIn($filter, $this->filters[$filter]);
            }
        }
//        dd($this->filters);
        //TODO: this should convert the json filter structure into query builder.
        return $query;
    }
}
