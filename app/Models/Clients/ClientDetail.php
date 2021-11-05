<?php

namespace App\Models\Clients;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientDetail extends Model
{
    use SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['client_id', 'detail', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array'
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Client', 'client_id', 'id');
    }
}
