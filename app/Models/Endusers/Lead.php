<?php

namespace App\Models\Endusers;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Lead extends Model
{
    use Notifiable, SoftDeletes, Uuid, HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id','client_id','first_name', 'last_name', 'email', 'mobile_phone', 'home_phone', 'gr_location_id', 'ip_address', 'lead_type'];

    public function details()
    {
        return $this->hasMany('App\Models\Endusers\LeadDetails', 'lead_id', 'id');
    }

    public function detailsDesc()
    {
        return $this->details()->orderBy('created_at', 'DESC');
    }
    public function detailsAsc()
    {
        return $this->details()->orderBy('created_at', 'ASC');
    }

    public function detail()
    {
        return $this->hasOne('App\Models\Endusers\LeadDetails', 'lead_id', 'id');
    }

    public function location()
    {
        return $this->hasOne('App\Models\Clients\Location', 'gymrevenue_id', 'gr_location_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Clients\Client', 'id', 'client_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('mobile_phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('lead_type', 'like', '%' . $search . '%')
                    ->orWhere('gr_location_id', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%')
                    ->orWhereHas('location', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('client', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($lead) {
            LeadDetails::create([
                'lead_id' => $lead->id,
                'client_id' => $lead->client_id,
                'field' => 'created',
                'value' => $lead->created_at
            ]);
        });
    }
}
