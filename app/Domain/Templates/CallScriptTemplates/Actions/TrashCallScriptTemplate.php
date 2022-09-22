<?php

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Domain\CallScripts\DripCallScripts\CallScriptTemplate;
use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashCallScriptTemplate
{
    use AsAction;

    public function handle(CallScriptTemplate $callscriptTemplate): CallScriptTemplate
    {
        CallScriptTemplateAggregate::retrieve($callscriptTemplate->id)->trash()->persist();

        return $callscriptTemplate->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('callscript-templates.trash', CallScriptTemplate::class);
    }

    public function asController(ActionRequest $request, CallScriptTemplate $callscriptTemplate): CallScriptTemplate
    {
        return $this->handle(
            $callscriptTemplate->id,
        );
    }

    public function htmlResponse(CallScriptTemplate $callscriptTemplate): RedirectResponse
    {
        Alert::success("CallScript Template '{$callscriptTemplate->name}' was sent to trash")->flash();

        return Redirect::route('mass-comms.callscript-templates');
    }
}
