<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class MarkLeadConverted
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_id' => 'required|int',
            'id' => 'required|int',
        ];
    }

    public function handle($data)
    {
        LeadAggregate::retrieve($data['id'])->convert($data['id'])->persist();

        return Lead::findOrFail($data['id']);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(Lead $lead)
    {
        Alert::success("Lead '{$lead->name}' was converted")->flash();
    }
}
