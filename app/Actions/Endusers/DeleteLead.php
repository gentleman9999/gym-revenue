<?php

namespace App\Actions\Endusers;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteLead
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($id, $current_user)
    {
        $deleted = Lead::withTrashed()->findOrFail($id);
        EndUserActivityAggregate::retrieve($id)->deleteLead($current_user->id ?? "Auto Generated", $id)->persist();
        return $deleted;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('leads.delete', Lead::class);
    }

    public function asController(Request $request, $id)
    {

        $lead = $this->handle(
            $id,
            $request->user(),
        );

        Alert::success("Lead '{$lead->name}' was deleted")->flash();

//        return Redirect::route('data.leads');
        return Redirect::back();
    }
}
