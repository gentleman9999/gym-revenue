<?php

namespace App\Domain\AgreementTemplates\Actions;

use App\Domain\AgreementTemplates\AgreementTemplateAggregate;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreAgreementTemplate
{
    use AsAction;

    public string $commandSignature = 'audience:restore {id}';
    public string $commandDescription = 'Restores the audience';

    public function handle(AgreementTemplate $agreement): AgreementTemplate
    {
        AgreementTemplateAggregate::retrieve($agreement->id)->restore()->persist();

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
