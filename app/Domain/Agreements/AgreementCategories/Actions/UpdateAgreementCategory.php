<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Actions;

use App\Domain\Agreements\AgreementCategories\AgreementCategoryAggregate;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAgreementCategory
{
    use AsAction;

    public function handle(AgreementCategory $agreement_category, array $payload): AgreementCategory
    {
        AgreementCategoryAggregate::retrieve($agreement_category->id)->update($payload)->persist();

        return $agreement_category->refresh();
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string'],
            'name' => ['required', 'string'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, AgreementCategory $agreement_category): AgreementCategory
    {
        $data = $request->validated();

        return $this->handle(
            $agreement_category,
            $data
        );
    }
}
