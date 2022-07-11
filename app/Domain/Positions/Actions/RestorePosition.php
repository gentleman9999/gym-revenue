<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestorePosition
{
    use AsAction;

    public string $commandSignature = 'audience:restore {id}';
    public string $commandDescription = 'Restores the audience';

    public function handle(string $id): Position
    {
        PositionAggregate::retrieve($id)->restore()->persist();

        return Position::withTrashed()->findOrFail($id);
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
