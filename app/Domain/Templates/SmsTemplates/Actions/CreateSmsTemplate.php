<?php

namespace App\Domain\Templates\SmsTemplates\Actions;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Templates\SmsTemplates\SmsTemplateAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateSmsTemplate
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
            'markup' => ['string', 'required', 'max:130'],
        ];
    }

    public function handle($data): SmsTemplate
    {
        $id = Uuid::new();

        SmsTemplateAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return SmsTemplate::findOrFail($id);
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

    public function asController(ActionRequest $request): SmsTemplate
    {
        return $this->handle(
            $request->validated(),
            $request->user()
        );
    }

    public function htmlResponse(SmsTemplate $template): RedirectResponse
    {
        Alert::success("SMS Template'{$template->name}' was created")->flash();

        return Redirect::route('comms.sms-templates.edit', $template->id);
    }
}
