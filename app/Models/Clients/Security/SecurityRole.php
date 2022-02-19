<?php

namespace App\Models\Clients\Security;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class SecurityRole extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['client_id', 'role_id', 'security_role', 'ability_ids', 'active', 'misc'];

    protected $casts = [
        'ability_ids' => 'array',
        'misc' => 'array'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function abilities()
    {
        return collect($this->ability_ids);
        //return Ability::findMany($this->ability_ids);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('security_role', 'like', '%' . $search . '%');
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
