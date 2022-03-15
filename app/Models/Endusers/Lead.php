<?php

namespace App\Models\Endusers;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Note;
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

    protected $fillable = ['id', 'client_id', 'first_name', 'last_name', 'gender', 'email', 'primary_phone', 'alternate_phone', 'gr_location_id', 'ip_address', 'lead_type_id', 'membership_type_id', 'lead_source_id'];

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

    public function trialMemberships()
    {
        return $this->hasMany(TrialMembership::class)->orderBy('start_date', 'DESC');;
    }

    public function leadType()
    {
        return $this->hasOne(LeadType::class, 'id', 'lead_type_id');
    }

    public function leadSource()
    {
        return $this->hasOne(LeadSource::class, 'id', 'lead_source_id');
    }

    public function lead_status()
    {
        return $this->detail()->whereField('lead_status')->whereActive(1);
    }

    public function membershipType()
    {
        return $this->hasOne(MembershipType::class, 'id', 'membership_type_id');
    }

    public function profile_picture()
    {
        return $this->detail()->whereField('profile_picture')->whereActive(1);
    }

    public function leadsclaimed()
    {
        return $this->details()->whereField('claimed');
    }

    public function lead_owner()
    {
        return $this->detail()->whereField('claimed')->whereActive(1);
    }

    public function middle_name()
    {
        return $this->detail()->whereField('middle_name')->whereActive(1);
    }

    public function dob()
    {
        return $this->detail()->whereField('dob')->whereActive(1);
    }

    public function opportunity()
    {
        return $this->detail()->whereField('opportunity')->whereActive(1);
    }

    public function agreementNumber()
    {
        return $this->detail()->whereField('agreement_number')->whereActive(1);
    }

    public function last_updated()
    {
        return $this->detail()->whereField('updated')->whereActive(1)
            ->orderBy('created_at', 'DESC');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'entity_id')->whereEntityType(self::class);
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
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('agreementNumber', function ($query) use ($search) {
                        $query->where('value', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('client', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
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
            $query->where('created_at', 'like', $createdat . '%');

            /* filters for typeoflead the data schema changed so lets get back to this */
        })->when($filters['typeoflead'] ?? null, function ($query, $typeoflead) {
            $query->whereIn('lead_type_id',  $typeoflead);

            /* Filter for Location(s) */
        })->when($filters['grlocation'] ?? null, function ($query, $grlocation) {
            $query->whereIn('gr_location_id',  $grlocation);

            /* Filter for Lead Sources */
        })->when($filters['leadsource'] ?? null, function ($query, $leadsource) {
            $query->whereIn('lead_source_id',  $leadsource);

            /* Filter for Lead Sources */
        })->when($filters['opportunity'] ?? null, function ($query, $opportunity) {

            $query->whereHas('opportunity', function ($query) use ($opportunity) {
                $query->whereIn('value',  $opportunity);
                //$query->where('value', '=', $opportunity); <- for single select
            });

        })->when($filters['leadsclaimed'] ?? null, function ($query, $leadsclaimed) {

            $query->whereHas('leadsclaimed', function ($query) use ($leadsclaimed) {
                $query->whereIn('value',  $leadsclaimed);
            });

        })->when($filters['dob'] ?? null, function ($query, $dob) {

            $query->whereHas('dob', function ($query) use ($dob) {
                $query->whereBetween('value', $dob);
            });
        })->when($filters['lastupdated'] ?? null, function ($query, $search) {

            $query->orderBy('updated_at', $search);

            /** Everything below already is redundant bc of the main search - but if it's in the ticket we do it. */
        })->when($filters['nameSearch'] ?? null, function ($query, $search) {

                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');

        })->when($filters['phoneSearch'] ?? null, function ($query, $search) {

            $query->where('primary_phone','like', '%' . $search . '%');

        })->when($filters['emailSearch'] ?? null, function ($query, $search) {

            $query->where('email','like', '%' . $search . '%');

        })->when($filters['agreementSearch'] ?? null, function ($query, $search) {

            $query->whereHas('agreementNumber', function ($query) use ($search) {
                $query->where('value', 'like', '%' . $search . '%');
            });

        });

    }

    public static function getDetailsTable()
    {
        return LeadDetails::class;
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
