<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Actions;

use App\Domain\Agreements\AgreementCategories\AgreementCategoryAggregate;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreAgreementCategory
{
    use AsAction;

    public function handle(AgreementCategory $agreement_category): AgreementCategory
    {
        AgreementCategoryAggregate::retrieve($agreement_category->id)->restore()->persist();

        return $agreement_category->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asCommand(Command $command): void
    {
        $agreement_category = $this->handle($command->argument('id'));
        $command->info('Restored Agreement Category' . $agreement_category->name);
    }
}
