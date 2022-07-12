<?php

namespace App\Domain\Audiences;

use App\Domain\Clients\Models\Client;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EventSourcing\Projections\Projection;

class Audience extends Projection
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['client_id'];

    protected $fillable = ['name', 'entity', 'filters'];

    protected $casts = [
        'filters' => 'array',
    ];

    public function getKeyName()
    {
        return 'id';
    }

    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getTextable()
    {
        return $this->getEntityBaseQuery()->whereUnsubscribedSms(0)->get();
    }

    public function getEmailable()
    {
        return $this->getEntityBaseQuery()->whereUnsubscribedEmail(0)->get();
    }

    protected function getEntityBaseQuery()
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
