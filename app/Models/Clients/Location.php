<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Location
 *
 * @mixin Builder
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'name'];

    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Client', 'id', 'client_id');
    }
}
