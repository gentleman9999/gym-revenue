<?php

declare(strict_types=1);

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAgreement
{
    use AsAction;

    public function handle(string $id): Agreement
    {
        $agreement = Agreement::withTrashed()->findOrFail($id);
        AgreementAggregate::retrieve($id)->delete()->persist();

        return $agreement;
    }

    public function asCommand(Command $command): void
    {
        $agreement = Agreement::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete Agreement '{$agreement->name}'? This cannot be undone")) {
            $agreement = $this->handle($command->argument('id'));
            $command->info('Deleted agreement ' . $agreement->name);

            return;
        }
        $command->info('Aborted deleting Agreement ' . $agreement->name);
    }
}
