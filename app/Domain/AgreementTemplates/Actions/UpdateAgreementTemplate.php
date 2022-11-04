<?php

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAgreementTemplate
{
    use AsAction;

    public function handle(AgreementTemplate $audience, array $payload): AgreementTemplate
    {
        AgreementTemplateAggregate::retrieve($audience->id)->update($payload)->persist();

        return $audience->refresh();
    }

    public function rules(): array
    {
        return [
            'gr_location_id' => ['sometimes', 'string'],
            'agreement_json' => ['sometimes', 'json'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, AgreementTemplate $agreement): AgreementTemplate
    {
        $data = $request->validated();

        return $this->handle(
            $agreement,
            $data
        );
    }
}
