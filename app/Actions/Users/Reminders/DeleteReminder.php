<?php

namespace App\Actions\Users\Reminders;

use App\Aggregates\Users\UserAggregate;
use App\Models\Reminder;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class DeleteReminder
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
            'remind_time' => ['int', 'required']
        ];
    }

    public function handle($data, $current_user = null)
    {
        if(!is_null($current_user)) {
            $client_id = $current_user->currentClientId();
            $data['client_id'] = $client_id;
        }

        $id = $data['id'];

        UserAggregate::retrieve($data['user_id'])->deleteReminder($current_user->id ?? "Auto Generated", $id)->persist();

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

        Alert::success("Reminder '{$reminder->name}' was created")->flash();

        return Redirect::back();
    }

}
