<?php

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateEmailTemplate
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
//            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['string', 'sometimes'],
            'subject' => ['string', 'sometimes'],
            'markup' => ['string', 'sometimes'],
            'json' => ['required'],
        ];
    }

    public function handle(EmailTemplate $template, array $data): EmailTemplate
    {
        EmailTemplateAggregate::retrieve($template->id)
            ->update($data)
            ->persist();

        return $template->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('email-templates.update', EmailTemplate::class);
    }

    public function asController(ActionRequest $request, EmailTemplate $emailTemplate): EmailTemplate
    {
        return $this->handle(
            $emailTemplate,
            $request->validated()
        );
    }

    public function htmlResponse(EmailTemplate $template): RedirectResponse
    {
        Alert::success("Email Template'{$template->name}' was updated")->flash();

        return Redirect::route('mass-comms.email-templates.edit', $template->id);
    }
}
