<?php

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Leads extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['client_id','first_name', 'last_name', 'email', 'mobile_phone', 'home_phone', 'gr_location_id', 'ip_address', 'lead_type'];

    public function details()
    {
        return $this->hasMany('App\Models\Endusers\LeadDetails', 'lead_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\Endusers\LeadDetails', 'lead_id', 'id');
    }
}
