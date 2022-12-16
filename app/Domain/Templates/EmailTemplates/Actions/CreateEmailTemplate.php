<?php

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Actions\GymRevAction;
use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateEmailTemplate extends GymRevAction
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
            'name' => [ 'required', 'string'],
            'subject' => ['required', 'string'],
            'markup' => ['required', 'string'],
            'json' => ['required'],
//            'thumbnail' => ['string', 'required']
        ];
    }

    public function handle(array $data): EmailTemplate
    {
        $id = Uuid::new();

        dd($data);

        EmailTemplateAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return EmailTemplate::findOrFail($id);
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
        Alert::success("Email Template'{$template->name}' was created")->flash();

        return Redirect::route('mass-comms.email-templates.edit', $template->id);
    }
}
