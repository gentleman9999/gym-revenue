<?php

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateCallScriptTemplate
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
           'client_id' => ['required', 'exists:clients,id'],
           'name' => ['string', 'required'],
           'script' => ['string', 'required'],
           'active' => ['sometimes'],
           'use_once' => ['sometimes'],
        ];
    }

    public function handle($data, $user = null): CallScriptTemplate
    {
        $id = Uuid::new();

        CallScriptTemplateAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return CallScriptTemplate::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('call-templates.create', CallScriptTemplate::class);
    }

    public function asController(ActionRequest $request): CallScriptTemplate
    {
        return $this->handle(
            $request->validated(),
            $request->user()
        );
    }

    public function htmlResponse(CallScriptTemplate $template): RedirectResponse
    {
        Alert::success("CallScript Template'{$template->name}' was created")->flash();

        return Redirect::route('mass-comms.call-templates.edit', $template->id);
    }
}
