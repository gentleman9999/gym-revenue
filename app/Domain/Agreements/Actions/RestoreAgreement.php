<?php

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreAgreement
{
    use AsAction;

    public string $commandSignature = 'audience:restore {id}';
    public string $commandDescription = 'Restores the audience';

    public function handle(Agreement $agreement): Agreement
    {
        AgreementAggregate::retrieve($agreement->id)->restore()->persist();

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
        $command->info('Restored Agreement ' . $agreement->name);
    }
}
