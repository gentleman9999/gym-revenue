<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\BatchUpsertEndUserApi;
use App\Domain\EndUsers\Actions\UpsertEndUserApi;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Http\Middleware\InjectClientId;

class BatchUpsertLeadApi extends BatchUpsertEndUserApi
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        $base_rules = parent::rules();

        return array_merge($base_rules, [
            '*.lead_source_id' => ['required', 'exists:lead_sources,id'],
            '*.lead_type_id' => ['required', 'exists:lead_types,id'],
        ]);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    protected static function getModel(): EndUser
    {
        return new Lead();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new LeadAggregate();
    }

    protected function getUpsertAction(): UpsertEndUserApi
    {
        return new UpsertLeadApi();
    }
}
