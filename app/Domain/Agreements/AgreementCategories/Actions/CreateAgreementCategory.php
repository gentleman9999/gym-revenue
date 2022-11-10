<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Actions;

use App\Domain\Agreements\AgreementCategories\AgreementCategoryAggregate;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAgreementCategory
{
    use AsAction;

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['string', 'required'],
        ];
    }

    public function handle(array $data): AgreementCategory
    {
        $id = Uuid::new();
        AgreementCategoryAggregate::retrieve((string)$id)->create($data)->persist();


        return AgreementCategory::findOrFail($id);
    }
}
