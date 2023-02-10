<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Actions;

use App\Actions\GymRevAction;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Templates\SmsTemplates\SmsTemplateAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateSmsTemplate extends GymRevAction
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
            'markup' => ['string', 'sometimes', 'required', 'max:130'],
        ];
    }

    public function handle(SmsTemplate $template, array $data): SmsTemplate
    {
        SmsTemplateAggregate::retrieve($template->id)->update($data)->persist();

        return $template->refresh();
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array
     */
    public function mapArgsToHandle(array $args): array
    {
        $smsTemplate = $args['input'];

        return [SmsTemplate::find($smsTemplate['id']), $smsTemplate];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('sms-templates.update', SmsTemplate::class);
    }

    public function asController(ActionRequest $request, SmsTemplate $smsTemplate): SmsTemplate
    {
        return $this->handle(
            $smsTemplate,
            $request->validated()
        );
    }

    public function htmlResponse(SmsTemplate $template): RedirectResponse
    {
        Alert::success("SMS Template'{$template->name}' was updated")->flash();

        return Redirect::route('mass-comms.sms-templates.edit', $template->id);
    }
}
