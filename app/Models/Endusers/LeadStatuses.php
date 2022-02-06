<?php


namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatuses extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $table = 'lead_statuses';

    public $incrementing = false;

    protected $fillable = ['id','client_id','status', 'order', 'active'];
}
