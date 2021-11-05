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

    protected $fillable = ['client_id', 'name', 'address1', 'address2', 'city', 'state', 'zip', 'active'];

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
}
