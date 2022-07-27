<?php

namespace App\Domain\Audiences\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreAudience
{
    use AsAction;

    public string $commandSignature = 'audience:restore {id}';
    public string $commandDescription = 'Restores the audience';

    public function handle(Audience $audience): Audience
    {
        AudienceAggregate::retrieve($audience->id)->restore()->persist();

        return $audience->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asCommand(Command $command): void
    {
        $audience = $this->handle($command->argument('id'));
        $command->info('Restored Audience ' . $audience->name);
    }
}
