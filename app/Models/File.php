<?php

namespace App\Models;

use App\Domain\Clients\Projections\Client;
use App\Models\Traits\Sortable;
use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Location
 *
 * @mixin Builder
 */
class File extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'client_id', 'user_id', 'filename', 'original_filename', 'extension', 'bucket', 'url', 'key', 'size', 'permissions', 'folder', 'is_hidden', 'visibility', 'type','fileable_type','fileable_id']; //'deleted_at'

    protected $casts = [
        'permissions' => 'array',
        'visibility' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return FileFactory::new();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getUrlAttribute()
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

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }



//    public function details()
//    {
//        return $this->hasMany(FileDetails::class);
//    }
//
//    public function detail()
//    {
//        return $this->hasOne(FileDetails::class);
//    }

    public function scopeFilter($query, array $filters)
    {
        $client_id = request()->user()->client_id;
        $security_group = request()->user()->securityGroup();

        $query->whereClientId($client_id)
        // ->whereUserId(null)
        ->whereIsHidden(false)
        ->whereFileableType(null)
        ->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('filename', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
