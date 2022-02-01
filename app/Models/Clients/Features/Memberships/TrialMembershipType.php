<?php

namespace App\Models\Clients\Features\Memberships;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrialMembershipType extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
       'id', 'client_id', 'type_name', 'slug', 'trial_length', 'locations', 'misc', 'active'
    ];

    protected $casts = [
        'locations' => 'array'
    ];
}
