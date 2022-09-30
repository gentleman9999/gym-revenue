<?php

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteCallScriptTemplate
{
    use AsAction;

    public function handle(CallScriptTemplate $callscriptTemplate): CallScriptTemplate
    {
        CallScriptTemplateAggregate::retrieve($callscriptTemplate->id)->delete()->persist();

        return $callscriptTemplate;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('call-templates.delete', CallScriptTemplate::class);
    }

    public function asController(ActionRequest $request): CallScriptTemplate
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(CallScriptTemplate $template): RedirectResponse
    {
        Alert::success("CallScript Template'{$template->name}' was deleted")->flash();

        return Redirect::back();
    }
}
