<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Actions;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DuplicateSmsTemplate
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
            'name' => ['string', 'sometimes'],
            'markup' => ['string', 'sometimes', 'max:130'],
        ];
    }

    public function handle(array $data): Model
    {
        return (new SmsTemplate())->scopeDuplicate(new SmsTemplate($data));
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
            $smsTemplate->toArray(),
        );
    }

    public function htmlResponse(SmsTemplate $template): RedirectResponse
    {
        Alert::success("SMS Template'{$template->name}' was duplicated")->flash();

        return Redirect::route('mass-comms.sms-templates', $template->id);
    }
}
