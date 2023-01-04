<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Domain\Locations\Projections\Location;
use App\Models\Endusers\MembershipType;
use App\Scopes\ClientScope;
use App\Scopes\EndUserScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EndUser extends User
{
    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new ClientScope());
        static::addGlobalScope(new EndUserScope());
    }

    public function detailsDesc(): HasMany
    {
        return $this->details()->orderBy('created_at', 'DESC');
    }

    public function detailsAsc(): HasMany
    {
        return $this->details()->orderBy('created_at', 'ASC');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'gymrevenue_id', 'home_location_id');
    }

    public function getMembershipTypeIdAttribute(): ?string
    {
        return $this->detail()->where('field', 'membership_type_id')->first()->value;
    }

    public function membershipType(): HasOne
    {
        return $this->hasOne(MembershipType::class, 'id', $this->membership_type_id);
    }

    public function claimed(): HasMany
    {
        return $this->details()->whereField('claimed');
    }

    public function getOwnerUserIdAttribute(): ?string
    {
        return $this->detail()->where('field', 'owner_user_id')->first()->value;
    }

    public function getOwnerAttribute(): ?Employee
    {
        return Employee::find($this->owner_user_id);
    }

    public function lastUpdated(): HasOne
    {
        return $this->detail()->whereField('updated')->whereActive(1)
            ->orderBy('created_at', 'DESC');
    }

    public function determineEndUserType(): string
    {
        $type = Lead::class;
        $agreements = Agreement::whereEndUserId($this->id)->get();
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

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $searchable_phrase = "%{$search}%";
            $query->where(function ($query) use ($search) {
                $query->where('phone', 'like', $searchable_phrase)
                    ->orWhere('email', 'like', $searchable_phrase)
                    ->orWhere('first_name', 'like', $searchable_phrase)
                    ->orWhere('last_name', 'like', $searchable_phrase)
                    ->orWhere('home_location_id', 'like', $searchable_phrase)
                    ->orWhere('ip_address', 'like', $searchable_phrase)
                    ->orWhere('agreement_id', 'like', $searchable_phrase)
                    ->orWhereHas('location', function ($query) use ($searchable_phrase) {
                        $query->where('name', 'like', $searchable_phrase);
                    })
                    ->orWhereHas('client', function ($query) use ($searchable_phrase) {
                        $query->where('name', 'like', $searchable_phrase);
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
            $query->whereIn('home_location_id',  $grlocation);

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
            $searchable_phrase = "%{$search}%";
            $query->where('first_name', 'like', $searchable_phrase)
                ->orWhere('last_name', 'like', $searchable_phrase);
        })->when($filters['phoneSearch'] ?? null, function ($query, $search) {
            $query->where('phone', 'like', '%' . $search . '%');
        })->when($filters['emailSearch'] ?? null, function ($query, $search) {
            $query->where('email', 'like', '%' . $search . '%');
        })->when($filters['agreementSearch'] ?? null, function ($query, $search) {
            $query->orWhere('agreement_id', 'like', '%' . $search . '%');
        });
    }

    public function isCBorGR(): bool
    {
        return (str_ends_with($this->email, '@capeandbay.com') || str_ends_with($this->email, '@gymrevenue.com'));
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany('App\Models\File', 'fileable');
    }
}
