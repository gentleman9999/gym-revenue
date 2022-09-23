<?php

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreCallScriptTemplate
{
    use AsAction;

    public function handle(CallScriptTemplate $callscriptTemplate): CallScriptTemplate
    {
        CallScriptTemplateAggregate::retrieve($callscriptTemplate->id)->restore()->persist();

        return $callscriptTemplate->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('callscript-templates.restore', CallScriptTemplate::class);
    }

    public function asController(ActionRequest $request, CallScriptTemplate $CallScriptTemplate): CallScriptTemplate
    {
        return $this->handle(
            $CallScriptTemplate->id,
        );
    }

    public function htmlResponse(CallScriptTemplate $CallScriptTemplate): RedirectResponse
    {
        Alert::success("CallScript Template'{$CallScriptTemplate->name}' was restored")->flash();

        return Redirect::route('mass-comms.callscript-templates');
    }
}
