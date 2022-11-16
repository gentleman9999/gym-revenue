<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Actions;

use App\Domain\Agreements\AgreementCategories\AgreementCategoryAggregate;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashAgreementCategory
{
    use AsAction;

    public function handle(AgreementCategory $agreement_category): AgreementCategory
    {
        $existing_agreements = Agreement::withoutTrashed()->whereAgreementCategoryId($agreement_category->id)->get();
        if ($existing_agreements) {
            return $agreement_category->refresh();
        }
        AgreementCategoryAggregate::retrieve($agreement_category->id)->trash()->persist();

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
        $command->info('Soft Deleted Agreement ' . $agreement_category->name);
    }

    public function htmlResponse(AgreementCategory $agreement_category): RedirectResponse
    {
        Alert::success("Agreement '{$agreement_category->name}' was created")->flash();

        return Redirect::route('agreement_categories.edit', $agreement_category->id);
    }
}
