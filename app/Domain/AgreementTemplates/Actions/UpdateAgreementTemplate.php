<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Enums\AgreementAvailabilityEnum;
use App\Http\Middleware\InjectClientId;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAgreementTemplate
{
    use AsAction;

    public function handle(AgreementTemplate $agreement_template, array $payload): AgreementTemplate
    {
        AgreementTemplateAggregate::retrieve($agreement_template->id)->update($payload)->persist();

        return $agreement_template->refresh();
    }

    public function rules(): array
    {
        return [
            'gr_location_id' => ['sometimes', 'string'],
            'agreement_json' => ['sometimes', 'json'],
            'billing_schedule' => ['required, exists:agreement_template_billing_schedule'],
            'is_not_billable' => ['sometimes', 'boolean'],
            'availability' => ['sometimes', 'string', new Enum(AgreementAvailabilityEnum::class)],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, AgreementTemplate $agreement): AgreementTemplate
    {
        return $this->handle(
            $agreement,
            $request->validated()
        );
    }
}
