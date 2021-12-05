<?php

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class LeadDetails extends Model
{
    use hasFactory, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $fk = 'lead_id';

    protected $fillable = ['client_id','lead_id', 'field', 'value', 'misc', 'active'];
//    protected $fillable = ['client_id','lead_id', 'field', 'value', 'misc', 'active', 'created_at'];//only needed for seeding data

    protected $casts = [
        'misc' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Client', 'client_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo('App\Models\Endusers\Lead', 'lead_id', 'id');
    }
}
