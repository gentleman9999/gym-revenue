<?php

namespace App\Domain\EndUsers\Projections;

use App\Domain\Clients\Models\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Stringable;

abstract class EndUserDetails extends GymRevProjection
{
    use hasFactory;
    use SoftDeletes;
    use Uuid;

    protected $fillable = ['field', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array',
    ];

    protected $hidden = ['client_id'];

    abstract public static function getRelatedModel(): EndUser;

    abstract public static function fk(): string;

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public static function getModelName(): Stringable
    {
        return str(class_basename((new static())::class));
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function endUser(): BelongsTo
    {
        return $this->belongsTo($this->getRelatedModel(), $this->fk(), 'id');
    }

    public static function createOrUpdateRecord(string $end_user_id, string $client_id, string $field, ?string $value, array $misc = null): void
    {
        $model = self::where(static::fk(), $end_user_id)->whereClientId($client_id)->whereField($field)->first();
        if ($model === null) {
//            $model = new self();
            $model = new static();
            $model->forceFill([
                static::fk() => $end_user_id,
                'client_id' => $client_id,
            ]);
            $model->field = $field;
            if ($misc) {
                $model->misc = $misc;
            }
        }
        $model->value = $value;
        $model->writeable()->save();
    }
}
