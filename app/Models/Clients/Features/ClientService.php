<?php

namespace App\Models\Clients\Features;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientService extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $fillable = ['feature_name', 'slug', 'active'];

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

}
