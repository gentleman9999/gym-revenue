<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TrashAgreementTemplate
{
    use AsAction;

    public function handle(AgreementTemplate $agreement): AgreementTemplate
    {
        AgreementTemplateAggregate::retrieve($agreement->id)->trash()->persist();

        return $agreement->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asCommand(Command $command): void
    {
        $agreement = $this->handle($command->argument('id'));
        $command->info('Soft Deleted Agreement ' . $agreement->name);
    }
}
