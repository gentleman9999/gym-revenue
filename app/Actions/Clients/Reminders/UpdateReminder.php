<?php

namespace App\Actions\Clients\Reminders;

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
            'name' => ['sometimes', 'string', 'required'],
            'id' => ['sometimes', 'string', 'required'],
            'description' => ['sometimes', 'string'],
            'remind_time' => ['sometimes', 'int'],
            'triggered_at' => ['sometimes', 'timestamp'],
        ];
    }

    public function handle($data, $current_user)
    {
        $client_id = $current_user->currentClientId();
        $data['client_id'] = $client_id;
        UserAggregate::retrieve($current_user->id)->updateReminder($data)->persist();

        return Reminder::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('reminders.update', Reminder::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $reminder = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Reminder '{$reminder->name}' was updated")->flash();

//        return Redirect::route('roles');
        return Redirect::route('reminders.edit', $id);
    }
}
