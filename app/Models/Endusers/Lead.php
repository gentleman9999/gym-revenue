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

    protected $fillable = ['id','client_id','first_name', 'last_name', 'email', 'primary_phone', 'alternate_phone', 'gr_location_id', 'ip_address', 'lead_type_id', 'membership_type_id', 'lead_source_id'];

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

    public function leadType()
    {
        return $this->hasOne(LeadType::class,  'id', 'lead_type_id');
    }

    public function leadSource()
    {
        return $this->hasOne(LeadSource::class, 'id', 'lead_source_id');
    }

    public function membershipType()
    {
        return $this->hasOne(MembershipType::class, 'id', 'membership_type_id');
    }

    public function services()
    {
        return $this->details()->whereField('service_id')->whereActive(1);
    }

    public function profile_picture()
    {
        return $this->detail()->whereField('profile_picture')->whereActive(1);
    }

    public function leadsclaimed()
    {
//$claimed =LeadDetails::whereClientId($client_id)->whereField('claimed')->get();
        return $this->details()->whereField('claimed');
    }



    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('primary_phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
//                    ->orWhere('lead_type', 'like', '%' . $search . '%')
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
/* created date will need a calendar date picker and the leads need different created_at dates */
        })->when($filters['createdat'] ?? null, function ($query, $createdat) {
            $query->where('created_at', 'like', $createdat.'%');
/* filters for typeoflead the data schema changed so lets get back to this */
        })->when($filters['typeoflead'] ?? null, function ($query, $typeoflead) {
            $query->where('lead_type_id', 'like', $typeoflead);
/* Filter for Location(s) */
        })->when($filters['grlocation'] ?? null, function ($query, $grlocation) {
            $query->where('gr_location_id', 'like', $grlocation.'%');
/* Filter for Lead Sources */
        })->when($filters['leadsource'] ?? null, function ($query, $leadsource) {
            $query->where('lead_source_id', 'like', $leadsource.'%');
  //          dd($query,$leadsource);
        })->when($filters['leadsclaimed'] ?? null, function ($query, $leadsclaimed)
        {

 $query->with('leadsclaimed');

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

    public static function getDetailsTable()
    {
        return LeadDetails::class;
    }
}
