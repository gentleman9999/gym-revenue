<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\UpsertEndUserApi;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Support\Uuid;
use Lorisleiva\Actions\ActionRequest;

class UpsertLeadApi extends UpsertEndUserApi
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
            'lead_source_id' => ['sometimes', 'exists:lead_sources,id'],
            'lead_type_id' => ['sometimes', 'exists:lead_types,id'],
            'lead_status' => 'sometimes|required|nullable|exists:lead_statuses,id',
        ]);
    }

    public function handle(array $data, $current_user = null)
    {
        $id = Uuid::new();//we should use uuid here

        if (array_key_exists('external_id', $data)) {
            $lead = Lead::whereEmail($data['email'])
                ->orWhere('external_id', $data['external_id'])
                ->first();
        } else {
            $lead = Lead::whereEmail($data['email'])
                ->first();
        }
        if (is_null($lead)) {
            $data['agreement_number'] = floor(time() - 99999999);
            $aggy = LeadAggregate::retrieve($id);
            $aggy->create($data);
        } else {
            $old_data = $lead->toArray();
            $aggy = LeadAggregate::retrieve($old_data['id']);
            $aggy->update($data);
        }

        try {
            $aggy->persist();
        } catch (\Exception $e) {
            dd($e);
        }

        return Lead::findOrFail($id);
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
