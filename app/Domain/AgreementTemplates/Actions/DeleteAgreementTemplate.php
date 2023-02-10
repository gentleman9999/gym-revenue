<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAgreementTemplate
{
    use AsAction;

    public function handle(string $id): AgreementTemplate
    {
        $agreement = AgreementTemplate::findOrFail($id);
        AgreementTemplateAggregate::retrieve($id)->delete()->persist();

        return $agreement;
    }

    public function asCommand(Command $command): void
    {
        $agreement = AgreementTemplate::findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete Agreement '{$agreement->name}'? This cannot be undone")) {
            $agreement = $this->handle($command->argument('id'));
            $command->info('Deleted agreement ' . $agreement->name);

            return;
        }
        $command->info('Aborted deleting Agreement ' . $agreement->name);
    }
}
