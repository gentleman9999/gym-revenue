<?php

namespace App\Actions\Users\Notifications;

use App\Models\Notification;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotifications
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
        return Notification::whereUserId($user->id)->orderByDesc('created_at')->paginate(50);
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
