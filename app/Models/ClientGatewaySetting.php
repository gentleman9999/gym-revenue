<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientGatewaySetting extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'client_id','gateway_provider', 'name', 'value'];
}
