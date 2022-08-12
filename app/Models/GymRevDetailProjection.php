<?php

namespace App\Models;

use App\Domain\Clients\Projections\Client;
use App\Scopes\ClientScope;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class GymRevDetailProjection extends GymRevProjection
{
    use hasFactory;
    use SoftDeletes;
    use Uuid;

    protected $casts = [
        'misc' => 'array',
    ];

    protected $hidden = ['client_id'];

    abstract public static function getRelatedModel();

    abstract public static function fk(): string;

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function parentProjection(): BelongsTo
    {
        return $this->belongsTo($this->getRelatedModel(), $this->fk(), 'id');
    }

    public static function createOrUpdateRecord(string $parent_id, string $field, ?string $value, array $misc = null): void
    {
        $model = self::where(static::fk(), $parent_id)->whereField($field)->first();
        if ($model === null) {
            $model = new static();
            $model->forceFill([ static::fk() => $parent_id ]);
            $model->field = $field;
            $model->client_id = ((new static())::getRelatedModel())::findOrFail($parent_id)->client_id;
        }
        $model->value = $value;
        if ($misc) {
            $model->misc = $misc;
        }
        $model->writeable()->save();
    }
}
