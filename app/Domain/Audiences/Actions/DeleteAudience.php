<?php

namespace App\Domain\Audiences\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAudience
{
    use AsAction;

    public string $commandSignature = 'audience:delete {id}';
    public string $commandDescription = 'Permanently deletes the Audience';

    public function handle(string $id): Audience
    {
        $audience = Audience::withTrashed()->findOrFail($id);
        AudienceAggregate::retrieve($id)->delete()->persist();

        return $audience;
    }

    public function asCommand(Command $command): void
    {
        $audience = Audience::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete Audience '{$audience->name}'? This cannot be undone")) {
            $audience = $this->handle($command->argument('id'));
            $command->info('Deleted audience ' . $audience->name);

            return;
        }
        $command->info('Aborted deleting audience ' . $audience->name);
    }
}
