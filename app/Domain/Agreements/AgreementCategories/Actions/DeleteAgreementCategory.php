<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Actions;

use App\Domain\Agreements\AgreementCategories\AgreementCategoryAggregate;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAgreementCategory
{
    use AsAction;

    public function handle(string $id): AgreementCategory
    {
        $agreement_category  = AgreementCategory::withTrashed()->findOrFail($id);
        $existing_agreements = Agreement::withoutTrashed()->whereAgreementCategoryId($id)->get();
        if ($existing_agreements) {
            return $agreement_category;
        }

        AgreementCategoryAggregate::retrieve($id)->delete()->persist();

        return $agreement_category;
    }

    public function asCommand(Command $command): void
    {
        $agreement_category = AgreementCategory::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete Agreement Category'{$agreement_category->name}'? This cannot be undone")) {
            $agreement_category = $this->handle($command->argument('id'));
            $command->info('Deleted agreement ' . $agreement_category->name);

            return;
        }
        $command->info('Aborted deleting Agreement Category ' . $agreement_category->name);
    }
}
