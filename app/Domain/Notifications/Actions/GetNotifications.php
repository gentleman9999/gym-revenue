<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotifications
{
    use AsAction;

    public function handle(User $user): Collection
    {
        return Notification::whereUserId($user->id)->orderByDesc('created_at')->paginate(50);
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        return true;
//    }

    public function asController(ActionRequest $request): Collection
    {
//        $data = $request->validated();
        return $this->handle(
            $request->user()
        );
//        return $notifications;
    }
}
