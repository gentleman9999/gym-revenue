<?php

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class LeadDetails extends Model
{
    use SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['client_id','lead_id', 'field', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Client', 'client_id', 'id');
    }
}
