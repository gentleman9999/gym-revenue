<?php

namespace App\Domain\Reminders\Actions;

use App\Domain\Reminders\Reminder;
use App\Domain\Users\UserAggregate;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateReminder
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
            'entity_type' => ['string', 'required'],
            'entity_id' => ['string', 'required'],
            'user_id' => ['int', 'required'],
            'name' => ['string', 'required'],
            'remind_time' => ['int', 'required'],
        ];
    }

    public function handle($id, $current_user = null)
    {
        if (! is_null($current_user)) {
            $client_id = $current_user->currentClientId();
            $data['client_id'] = $client_id;
        }


        UserAggregate::retrieve($data['user_id'])->updateReminder($data)->persist();

        return Reminder::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $reminder = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Reminder '{$reminder->name}' was updated")->flash();

        return Redirect::back();
    }
}