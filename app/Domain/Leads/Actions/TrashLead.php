<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\LeadAggregate;
use App\Domain\Leads\Models\Lead;
use Illuminate\Http\RedirectResponse;
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
            'reason' => ['required','string'],
        ];
    }

    public function handle(Lead $lead, $reason)
    {
        LeadAggregate::retrieve($lead->id)->trash($reason)->persist();

        return $lead->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.trash', Lead::class);
    }

    public function asController(ActionRequest $request, Lead $lead)
    {
        $this->handle(
            $lead,
            $request->validated()['reason']
        );

        return $lead;
    }

    public function htmlResponse(Lead $lead): RedirectResponse
    {
        Alert::success("Location '{$lead->name}' sent to trash")->flash();

        return Redirect::back();
    }
}
