<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteEmailTemplate
{
    use AsAction;

    public function handle(EmailTemplate $emailTemplate): EmailTemplate
    {
        EmailTemplateAggregate::retrieve($emailTemplate->id)->delete()->persist();

        return $emailTemplate;
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('email-templates.create', EmailTemplate::class);
    }

    public function asController(ActionRequest $request): EmailTemplate
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(EmailTemplate $template): RedirectResponse
    {
        Alert::success("Email Template'{$template->name}' was deleted")->flash();

        return Redirect::back();
    }
}
