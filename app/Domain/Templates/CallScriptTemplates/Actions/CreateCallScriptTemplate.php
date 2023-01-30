<?php

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Actions\GymRevAction;
use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateCallScriptTemplate extends GymRevAction
{
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
           'active' => ['sometimes', 'boolean'],
           'use_once' => ['sometimes', 'boolean'],
           'json' => ['sometimes', 'array'],
           'team_id' => ['sometimes', 'nullable', 'exists:teams,id'],
        ];
    }

    public function mapArgsToHandle($args): array
    {
        return [$args['input']];
    }

    public function handle(array $data): CallScriptTemplate
    {
        $id = Uuid::new();

        CallScriptTemplateAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return CallScriptTemplate::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('call-templates.create', CallScriptTemplate::class);
    }

    public function asController(ActionRequest $request): CallScriptTemplate
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(CallScriptTemplate $template): RedirectResponse
    {
        Alert::success("CallScript Template'{$template->name}' was created")->flash();

        return Redirect::route('mass-comms.call-templates');
    }
}
