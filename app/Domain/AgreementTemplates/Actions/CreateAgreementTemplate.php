<?php

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreementTemplate
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
            'billing_schedule' => ['required, exists:agreement_template_billing_schedule'],
            'agreement_json' => ['required','json'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function handle(array $data): AgreementTemplate
    {
        $id = Uuid::new();
        AgreementTemplateAggregate::retrieve($id)->create($data)->persist();

        return AgreementTemplate::findOrFail($id);
    }

    public function asController(ActionRequest $request): AgreementTemplate
    {
        $data = $request->validated();

        return ($this->handle($data));
    }
}
