<?php

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDetails extends Model
{
    use hasFactory;
    use SoftDeletes;
    use Uuid;

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
        return $this->belongsTo('App\Domain\Clients\Models\Client', 'client_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo('App\Models\Endusers\Lead', 'lead_id', 'id');
    }

    public static function createOrUpdateRecord($lead_id, $client_id, $field, $value)
    {
        $model = self::firstOrCreate([
            'lead_id' => $lead_id,
            'client_id' => $client_id,
            'field' => $field,
        ]);

        $model->value = $value;
        $model->save();
    }
}
