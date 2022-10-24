<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Models\Chat;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetChat
{
    use AsAction;

    public string $commandSignature = 'chat:get {user_id}';

    public function handle(int $user_id): LengthAwarePaginator
    {
        return Chat::with(['participants.user', 'messages'])
            ->whereHas('participants', static fn (Builder $b) => $b->where(['user_id' => $user_id]))
            ->limit(50)
            ->paginate();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('chat.create', Chat::class);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->user()->id);
    }

    public function asCommand(Command $command)
    {
        $this->handle((int) $command->argument('user_id'));
    }
}
