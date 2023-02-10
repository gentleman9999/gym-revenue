<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Actions\GymRevAction;
use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateCallScriptTemplate extends GymRevAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'sometimes'],
            'subject' => ['string', 'sometimes'],
            'active' => ['sometimes', 'boolean'],
            'use_once' => ['sometimes', 'boolean'],
            'json' => ['sometimes', 'array'],
        ];
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array
     */
    public function mapArgsToHandle(array $args): array
    {
        $call_script_template = $args['input'];

        return [CallScriptTemplate::find($call_script_template['id']), $call_script_template];
    }

    public function handle(CallScriptTemplate $template, array $data): CallScriptTemplate
    {
        CallScriptTemplateAggregate::retrieve($template->id)
            ->update($data)
            ->persist();

        return $template->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('call-templates.update', CallScriptTemplate::class);
    }

    public function asController(ActionRequest $request, CallScriptTemplate $call_template): CallScriptTemplate
    {
        return $this->handle(
            $call_template,
            $request->validated()
        );
    }

    public function htmlResponse(CallScriptTemplate $template): RedirectResponse
    {
        Alert::success("CallScript Template'{$template->name}' was updated")->flash();

        return Redirect::route('mass-comms.call-templates');
    }
}
