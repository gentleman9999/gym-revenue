<?php

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreementTemplate
{
    use AsAction;

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
}
