<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\CreateEndUserApi;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use Lorisleiva\Actions\ActionRequest;

class CreateLeadApi extends CreateEndUserApi
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
            'lead_source_id' => ['required', 'exists:lead_sources,id'],
            'lead_type_id' => ['required', 'exists:lead_types,id'],
            'lead_status' => 'sometimes|required|nullable|exists:lead_statuses,id',
        ]);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }

    protected static function getModel(): EndUser
    {
        return new Lead();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new LeadAggregate();
    }
}
