<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashEmailTemplate
{
    use AsAction;

    public function handle(EmailTemplate $emailTemplate): EmailTemplate
    {
        EmailTemplateAggregate::retrieve($emailTemplate->id)->trash()->persist();

        return $emailTemplate->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('email-templates.trash', EmailTemplate::class);
    }

    public function asController(EmailTemplate $emailTemplate): EmailTemplate
    {
        return $this->handle(
            $emailTemplate,
        );
    }

    public function htmlResponse(EmailTemplate $emailTemplate): RedirectResponse
    {
        Alert::success("Email Template '{$emailTemplate->name}' was sent to trash")->flash();

        return Redirect::route('mass-comms.email-templates');
    }
}
