<?php

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Notification;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUnreadNotificationCount
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($user)
    {
        //TODO: is this efficient as possible? is it actually doing a db count or counting the results?
        return Notification::whereUserId($user->id)->count();
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        return true;
//    }

    public function asController(ActionRequest $request)
    {
//        $data = $request->validated();
        return $this->handle(
            $request->user()
        );
//        return $notifications;
    }
}
