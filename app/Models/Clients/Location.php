<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location
 *
 * @mixin Builder
 */
class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['client_id', 'name', 'address1', 'address2', 'city', 'state', 'zip', 'active', 'location_no', 'gymrevenue_id', 'deleted_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function details()
    {
        return $this->hasMany(LocationDetails::class);
    }

    public function detail()
    {
        return $this->hasOne(LocationDetails::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('city', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['state'] ?? null, function($query, $state) {
$query->where('state','like', '%'.$state.'%');
        });
    }
}
