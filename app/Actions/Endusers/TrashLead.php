<?php

namespace App\Actions\Endusers;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Endusers\Lead;
use Bouncer;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashLead
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
            'reason' => ['required','string']
        ];
    }

    public function handle($id, $current_user, $reason)
    {
        EndUserActivityAggregate::retrieve($id)->trashLead2($reason, $current_user->id ?? "Auto Generated")->persist();

        return Lead::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('leads.trash', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $this->handle(
            $id,
            $request->user(),
            $request->validated()['reason']
        );

        Alert::success("Location '{$lead->name}' sent to trash")->flash();

//        return Redirect::route('data.leads');
        return Redirect::back();
    }
}
