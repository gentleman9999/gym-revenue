<?php

namespace App\Actions\Users\Reminders;

use App\Aggregates\Users\UserAggregate;
use App\Models\Reminder;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class DeleteReminderWithoutID
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
            'event_id' => ['string', 'required'],
            'entity_type' => ['string', 'required'],
            'user_id' => ['int', 'required'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        if(!is_null($current_user)) {
            $client_id = $current_user->currentClientId();
            $data['client_id'] = $client_id;
        }

        $reminder = Reminder::whereEntityType($data['entity_type'])->whereEntityId($data['entity_id'])->whereUserId($data['user_id'])->first();
        if(is_null($reminder)) {
            return true;
        } else {
            $id = $reminder->id;

            UserAggregate::retrieve($data['user_id'])->deleteReminder($current_user->id ?? "Auto Generated", $id)->persist();

            return true;
        }
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

        Alert::success("Reminder '{$reminder->name}' was deleted")->flash();

        return Redirect::back();
    }

}
