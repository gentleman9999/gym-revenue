<?php

declare(strict_types=1);

namespace App\Domain\Agreements\Projections;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['client_id', 'gr_location_id', 'created_by', 'agreement_category_id', 'user_id', 'agreement_template_id', 'active', 'contract_file_id'];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function categoryById(): HasOne
    {
        return $this->HasOne(AgreementCategory::class, 'id', 'agreement_category_id');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'gymrevenue_id', 'gr_location_id');
    }

    public function template(): HasOne
    {
        return $this->hasOne(AgreementTemplate::class, 'id', 'agreement_template_id');
    }
}
