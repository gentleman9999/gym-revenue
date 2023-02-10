<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Actions;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Templates\SmsTemplates\SmsTemplateAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashSmsTemplate
{
    use AsAction;

    public function handle(SmsTemplate $smsTemplate): SmsTemplate
    {
        SmsTemplateAggregate::retrieve($smsTemplate->id)->trash()->persist();

        return $smsTemplate->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('sms-templates.create', SmsTemplate::class);
    }

    public function asController(ActionRequest $request, SmsTemplate $smsTemplate): SmsTemplate
    {
        return $this->handle(
            $smsTemplate,
        );
    }

    public function htmlResponse(SmsTemplate $smsTemplate): RedirectResponse
    {
        Alert::success("SMS Template '{$smsTemplate->name}' was sent to trash")->flash();

        return Redirect::back();
    }
}
