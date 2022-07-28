<?php

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateEmailTemplate
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
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['string', 'required'],
            'subject' => ['string', 'required'],
            'markup' => ['string', 'required'],
            'json' => ['required'],
//            'thumbnail' => ['string', 'required']
        ];
    }

    public function handle($data, $user = null): EmailTemplate
    {
        $id = Uuid::new();

        EmailTemplateAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return EmailTemplate::findOrFail($id);
    }

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
            $request->validated(),
            $request->user()
        );
    }

    public function htmlResponse(EmailTemplate $template): RedirectResponse
    {
        Alert::success("Email Template'{$template->name}' was created")->flash();

        return Redirect::route('comms.email-templates.edit', $template->id);
    }
}
