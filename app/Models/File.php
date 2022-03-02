<?php

namespace App\Models;

use App\Aggregates\Clients\FileAggregate;
use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location
 *
 * @mixin Builder
 */
class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'client_id', 'user_id', 'filename', 'original_filename', 'extension', 'bucket', 'url', 'key', 'size', 'isPublic']; //'deleted_at'

    public function client()
    {
        return $this->belongsTo(Client::class);
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
        $query->when($filters['search'] ?? null, function ($query, $search) {
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
