<?php

namespace App\Models;

use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\EventSourcing\Projections\Projection;

class Position extends Projection
{
    use HasFactory;
    use Sortable;

    protected $fillable = ['client_id', 'name'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_position', 'position_id', 'department_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
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
