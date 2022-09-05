<?php

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Domain\Campaigns\DripCampaigns\EmailTemplate;
use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreEmailTemplate
{
    use AsAction;

    public function handle(EmailTemplate $emailTemplate): EmailTemplate
    {
        EmailTemplateAggregate::retrieve($emailTemplate->id)->restore()->persist();

        return $emailTemplate->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('email-templates.create', EmailTemplate::class);
    }

    public function asController(ActionRequest $request, EmailTemplate $emailTemplate): EmailTemplate
    {
        return $this->handle(
            $emailTemplate->id,
        );
    }

    public function htmlResponse(EmailTemplate $emailTemplate): RedirectResponse
    {
        Alert::success("Email Template'{$emailTemplate->name}' was restored")->flash();

        return Redirect::route('mass-comms.email-templates');
    }
}
