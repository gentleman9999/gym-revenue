<?php

namespace App\Domain\Agreements\Projections;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Locations\Projections\Location;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['client_id', 'gr_location_id', 'created_by', 'agreement_json'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(AgreementCategory::class, 'id', 'agreement_category_id');
    }

    public function endUser(): HasOne
    {
        return $this->hasOne(EndUser::class, 'id', 'end_user_id');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'gymrevenue_id', 'gr_location_id');
    }
}
