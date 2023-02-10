<?php

declare(strict_types=1);

namespace App\Domain\Audiences;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Database\Factories\AudienceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @method static AudienceFactory factory()
 */
class Audience extends GymRevProjection
{
    use SoftDeletes;
    use HasFactory;

    /** @var array<string>  */
    protected $hidden = ['client_id'];

    /** @var array<string>  */
    protected $fillable = ['name', 'entity', 'filters', 'editable'];

    /** @var array<string, string>  */
    protected $casts = [
        'filters' => 'array',
    ];

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

    public function getCallable(): Collection
    {
        return $this->getEntityBaseQuery()->whereNotNull('phone')->get();
    }

    protected function getEntityBaseQuery(): Builder
    {
        $entity = new $this->entity();

        $query = $entity::withoutGlobalScopes()
            ->whereClientId($this->client_id)
            ->when($this->filters !== null, function (Builder $query): void {
                $this->generateQueryFromFilters($query);
            });

        return $query;
    }

    protected function generateQueryFromFilters(Builder $query): Builder
    {
        $entity = (new ($this->entity));
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
        return AudienceFactory::new();
    }
}
