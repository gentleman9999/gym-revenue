<?php

namespace App\Domain\EndUsers\Projections;

use App\Domain\Agreements\Projections\Agreement;
use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Customers\Projections\Customer;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamDetail;
use App\Domain\Users\Models\User;
use App\Models\Endusers\MembershipType;
use App\Models\GymRevProjection;
use App\Models\Note;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 */
class EndUser extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;
    //Adding comment as a test

    protected array $shared_fillable = [
        'first_name', 'middle_name', 'last_name', 'gender',
        'primary_phone', 'alternate_phone', 'gr_location_id',
        'ip_address', 'membership_type_id', 'date_of_birth',
        'opportunity', 'misc', 'owner_user_id', 'profile_picture',
    ];

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'gender',
        'primary_phone', 'alternate_phone', 'gr_location_id',
        'ip_address', 'membership_type_id', 'date_of_birth',
        'opportunity', 'misc', 'owner_user_id', 'profile_picture',
    ];

    protected $casts = [
        'profile_picture' => 'array',
        'misc' => 'array',
    ];

    protected $hidden = ['client_id'];

    public function getFillable(): array
    {
        return array_merge($this->shared_fillable, $this->fillable);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(EndUserDetails::class, "end_user_id", 'id');
    }

    public function detailsDesc(): HasMany
    {
        return $this->details()->orderBy('created_at', 'DESC');
    }

    public function detailsAsc(): HasMany
    {
        return $this->details()->orderBy('created_at', 'ASC');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(EndUserDetails::class, "end_user_id", 'id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'gymrevenue_id', 'gr_location_id');
    }

    public function membershipType(): HasOne
    {
        return $this->hasOne(MembershipType::class, 'id', 'membership_type_id');
    }

    public function claimed(): HasMany
    {
        return $this->details()->whereField('claimed');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id', 'id');
    }

    public function last_updated(): HasOne
    {
        return $this->detail()->whereField('updated')->whereActive(1)
            ->orderBy('created_at', 'DESC');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'entity_id')->whereEntityType(self::class);
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('primary_phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('gr_location_id', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%')
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
            /* created date will need a calendar date picker and the endusers need different created_at dates */
        })->when($filters['createdat'] ?? null, function ($query, $createdat) {
            $query->where('created_at', 'like', $createdat . '%');

            /* filters for typeof lenduser the data schema changed so lets get back to this */
        })->when($filters['grlocation'] ?? null, function ($query, $grlocation) {
            $query->whereIn('gr_location_id',  $grlocation);

            /* Filter for EndUser Sources */
        })->when($filters['opportunity'] ?? null, function ($query, $opportunity) {
            $query->whereIn('opportunity',  $opportunity);
        })->when($filters['claimed'] ?? null, function ($query, $owner_user_id) {
            $query->whereOwnerUserId($owner_user_id);
        })->when($filters['date_of_birth'] ?? null, function ($query, $dob) {
            $query->whereBetween('date_of_birth', $dob);
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
            $query->orWhere('agreement_number', 'like', '%' . $search . '%');
        });
    }

    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getPreviewNote()
    {
        return Note::select('note')->whereEntityId($this->id)->get();
    }

    public function getClubLocation()
    {
        return Location::where('gymrevenue_id', $this->gr_location_id)->first();
    }

    public function getLocations()
    {
        $client_id = $this->client_id;
        $is_client_user = $client_id !== null;
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();

        return $locations_records;
    }

    public function getPhoneNumber(): string
    {
        return $this->primary_phone;
    }

    private function setUpLocationsObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];
        /**
         * BUSINESS RULES
         * 1. All Locations
         *  - Cape & Bay user
         *  - The active_team is the current client's default_team (gets all the client's locations)
         * 2. Scoped Locations
         *  - The active_team is not the current client's default_team
         *      so get the teams listed in team_details
         * 3. No Locations
         *  - The active_team is not the current client's default_team
         *      but there are no locations assigned in team_details
         *  - (Bug or Feature?) - The current client is null (cape & bay)
         *      but the user is not a cape & bay user.
         */

        if ((! is_null($client_id))) {
            $session_team = session()->get('current_team');
            if ($session_team && array_key_exists('id', $session_team)) {
                $current_team = Team::find($session_team['id']);
            } else {
                $current_team = Team::find(auth()->user()->default_team_id);
            }
            $client = Client::find($client_id);

            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team->id == $client->home_team_id) {
                $results = new Location();
            } else {
                // The active_team is not the current client's default_team
                $team_locations = TeamDetail::whereTeamId($current_team->id)
                    ->where('field', '=', 'team-location')
                    ->get();

                if (count($team_locations) > 0) {
                    $in_query = [];
                    // so get the teams listed in team_details
                    foreach ($team_locations as $team_location) {
                        $in_query[] = $team_location->value;
                    }

                    $results = Location::whereIn('gymrevenue_id', $in_query);
                }
            }
        } else {
            // Cape & Bay user
            if (! $is_client_user) {
                $results = new Location();
            }
        }

        return $results;
    }

    public function determineEndUserType(): string
    {
        $type = Lead::class;
        $agreements = Agreement::whereEndUserId($this->id)->get();
//        TODO: this in incorrect. an has 1 active of type membership, then member. if active and no membership, customer
        foreach ($agreements as $agreement) {
            if ($agreement->active) {
                $type = Member::class;
            }
            if ($type != Member::class) {
                $type = Customer::class;
            }
        }

        return $type;
    }

    public function isCBorGR(): bool
    {
        return (str_ends_with($this->email, '@capeandbay.com') || str_ends_with($this->email, '@gymrevenue.com'));
    }

    //    TODO: store as projection somewhere, this is expensive
    public function getInteractionCount()
    {
        $aggy = EndUserAggregate::retrieve($this->id);

        return $aggy->getInteractionCount();
    }
}
