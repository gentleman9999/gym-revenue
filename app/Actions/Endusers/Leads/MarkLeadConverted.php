<?php

namespace App\Actions\Endusers\Leads;

use App\Aggregates\Endusers\LeadAggregate;
use App\Models\Endusers\Lead;
use Illuminate\Support\Facades\Redirect;
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
        ];
    }

    public function handle($data)
    {
        LeadAggregate::retrieve($data['id'])->convert($data);

        return Lead::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.edit', Lead::class);
    }

    public function asController(ActionRequest $request)
    {
        $lead = $this->handle(
            $request->validated(),
        );

        Alert::success("Lead '{$lead->name}' was converted")->flash();

        return Redirect::route('data.leads');
    }
}
