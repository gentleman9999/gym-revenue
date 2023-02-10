<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Clients\Projections\Client;
use App\Models\Traits\Sortable;
use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Location
 *
 * @mixin Builder
 */
//TODO: this needs to be refactored to use gymRevProjection and moved to Domain\Files
class File extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = [
        'id',
        'client_id',
        'user_id',
        'filename',
        'original_filename',
        'extension',
        'bucket',
        'url',
        'key',
        'size',
        'permissions',
        'folder',
        'is_hidden',
        'visibility',
        'type',
        'fileable_type',
        'fileable_id',
    ]; //'deleted_at'

    /** @var array<string, string> */
    protected $casts = [
        'permissions' => 'array',
        'visibility' => 'boolean',
    ];

//TODO: 'id' and 'client_id' should not be mass assignable.


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->visibility) {
            return Storage::disk('s3')->temporaryUrl($this->original['key'], now()->addMinutes(10));
        } else {
            return $this->original['url'];
        }
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return void
     */
    public function scopeIsVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $client_id = request()->user()->client_id;
//        $security_group = request()->user()->securityGroup();

        $query->whereClientId($client_id)
            // ->whereUserId(null)
            ->whereIsHidden(false)->whereFileableType(null)->when($filters['search'] ?? null, function (
                $query,
                $search
            ): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('filename', 'like', '%' . $search . '%');
                });
            })->when($filters['trashed'] ?? null, function ($query, $trashed): void {
                if ($trashed === 'with') {
                    $query->withTrashed();
                } elseif ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            });
    }

    protected static function newFactory(): Factory
    {
        return FileFactory::new();
    }
}
