<?php

namespace App\Domain\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['user_id', 'state', 'text', 'misc', 'entity_id', 'entity_type', 'entity'];

    protected $casts = [
        'misc' => 'array',
        'entity' => 'array',
    ];

    protected function user()
    {
        return $this->belongsTo(User::class);
    }
}
