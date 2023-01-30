<?php

namespace App\Domain\Templates\SmsTemplates\Actions;

use App\Actions\GymRevAction;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Templates\SmsTemplates\SmsTemplateAggregate;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateSmsTemplate extends GymRevAction
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
            'markup' => ['string', 'required', 'max:130'],
        ];
    }

    public function mapArgsToHandle($args): array
    {
        return [$args['input']];
    }

    public function handle(array $data): SmsTemplate
    {
        $id = Uuid::new();

        SmsTemplateAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return SmsTemplate::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('sms-templates.create', SmsTemplate::class);
    }

    public function asController(ActionRequest $request): SmsTemplate
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(SmsTemplate $template): RedirectResponse
    {
        Alert::success("SMS Template'{$template->name}' was created")->flash();

        return Redirect::route('mass-comms.sms-templates.edit', $template->id);
    }
}
