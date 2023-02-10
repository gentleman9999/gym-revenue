<?php

declare(strict_types=1);

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TrashAgreement
{
    use AsAction;

    public function handle(Agreement $agreement): Agreement
    {
        AgreementAggregate::retrieve($agreement->id)->trash()->persist();

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
