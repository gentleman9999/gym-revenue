<?php

declare(strict_types=1);

namespace App\Domain\BillingSchedules\Projections;

use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Domain\Clients\Projections\Client;
use App\Enums\BillingScheduleTypesEnum;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingSchedule extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['client_id', 'type', 'is_open_ended', 'is_renewable', 'should_renew_automatically', 'term_length', 'min_terms','amount'];

    protected $casts = [
        'type' => BillingScheduleTypesEnum::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function agreementTemplate(): BelongsToMany
    {
        return $this->belongsToMany(AgreementTemplate::class, 'agreement_template_billing_schedule', 'agreement_template_id', 'billing_schedule_id');
    }
}
