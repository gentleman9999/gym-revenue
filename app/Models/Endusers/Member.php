<?php

namespace App\Models\Endusers;

use App\Models\Note;
use App\Models\Traits\Sortable;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Member extends Model
{
    use Notifiable, SoftDeletes, Uuid, HasFactory, Sortable;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'client_id', 'first_name', 'middle_name', 'last_name', 'gender', 'email', 'primary_phone', 'alternate_phone', 'gr_location_id', 'profile_picture', 'agreement_number', 'gender', 'date_of_birth'];

    protected $casts = ['profile_picture' => 'array'];

//    public function details()
//    {
//        return $this->hasMany('App\Models\Endusers\MemberDetails', 'member_id', 'id');
//    }

//    public function detailsDesc()
//    {
//        return $this->details()->orderBy('created_at', 'DESC');
//    }

//    public function detailsAsc()
//    {
//        return $this->details()->orderBy('created_at', 'ASC');
//    }

//    public function detail()
//    {
//        return $this->hasOne('App\Models\Endusers\MemberDetails', 'member_id', 'id');
//    }

    public function location()
    {
        return $this->hasOne('App\Models\Clients\Location', 'gymrevenue_id', 'gr_location_id');
    }

    public function client()
    {
        return $this->hasOne('App\Models\Clients\Client', 'id', 'client_id');
    }

    public function membershipType()
    {
        return $this->hasOne(MembershipType::class, 'id', 'membership_type_id');
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
                    ->orWhere('gr_location_id', 'like', '%' . $search . '%')
                    ->orWhere('agreement_number', 'like', '%' . $search . '%')
                    ->orWhereHas('location', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
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
            $query->whereIn('lead_type_id', $typeoflead);

            /* Filter for Location(s) */
        })->when($filters['grlocation'] ?? null, function ($query, $grlocation) {
            $query->whereIn('gr_location_id', $grlocation);

            /* Filter for Lead Sources */
        })->when($filters['leadsource'] ?? null, function ($query, $leadsource) {
            $query->whereIn('lead_source_id', $leadsource);

            /* Filter for Lead Sources */
        })->when($filters['opportunity'] ?? null, function ($query, $opportunity) {

            $query->whereHas('opportunity', function ($query) use ($opportunity) {
                $query->whereIn('value', $opportunity);
                //$query->where('value', '=', $opportunity); <- for single select
            });

        })->when($filters['leadsclaimed'] ?? null, function ($query, $leadsclaimed) {

            $query->whereHas('leadsclaimed', function ($query) use ($leadsclaimed) {
                $query->whereIn('value', $leadsclaimed);
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

            $query->where('primary_phone', 'like', '%' . $search . '%');

        })->when($filters['emailSearch'] ?? null, function ($query, $search) {

            $query->where('email', 'like', '%' . $search . '%');

        })->when($filters['agreementSearch'] ?? null, function ($query, $search) {

            $query->whereHas('agreementNumber', function ($query) use ($search) {
                $query->where('value', 'like', '%' . $search . '%');
            });

        });

    }

//    public static function getDetailsTable()
//    {
//        return MemberDetails::class;
//    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
