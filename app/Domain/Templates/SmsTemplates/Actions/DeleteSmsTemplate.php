<?php

namespace App\Domain\Templates\SmsTemplates\Actions;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Templates\SmsTemplates\SmsTemplateAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteSmsTemplate
{
    use AsAction;

    public function handle(SmsTemplate $smsTemplate): SmsTemplate
    {
        SmsTemplateAggregate::retrieve($smsTemplate->id)->delete()->persist();

        return $smsTemplate;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('sms-templates.create', SmsTemplate::class);
    }

    public function asController(ActionRequest $request, SmsTemplate $smsTemplate): SmsTemplate
    {
        return $this->handle(
            $smsTemplate
        );
    }

    public function htmlResponse(SmsTemplate $template): RedirectResponse
    {
        Alert::success("SMS Template'{$template->name}' was deleted")->flash();

        return Redirect::back();
    }
}
