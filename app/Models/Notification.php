<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'user_id', 'state', 'entity_type', 'text', 'dismissed_at', 'misc', 'entity_id', 'entity_type', 'entity'];

    protected $casts = [
        'misc' => 'array',
        'entity' => 'array',
    ];

    protected function user()
    {
        return $this->belongsTo(User::class);
    }

}
