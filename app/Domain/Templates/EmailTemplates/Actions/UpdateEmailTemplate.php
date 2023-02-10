<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Actions\GymRevAction;
use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateEmailTemplate extends GymRevAction
{
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

    /**
     * @param array<string, mixed> $args
     *
     * @return array
     */
    public function mapArgsToHandle(array $args): array
    {
        $emailTemplate = $args['input'];

        return [EmailTemplate::find($emailTemplate['id']), $emailTemplate];
    }

    public function handle(EmailTemplate $template, array $data): EmailTemplate
    {
        EmailTemplateAggregate::retrieve($template->id)
            ->update($data)
            ->persist();

        return $template->refresh();
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
