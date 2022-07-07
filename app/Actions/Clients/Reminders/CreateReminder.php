<?php

namespace App\Actions\Clients\Reminders;

use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateReminder
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
            'name' => ['string', 'required'],
            'entity_type' => ['string', 'somtimes', 'nullable'],
            'entity_id' => ['string', 'somtimes', 'nullable'],
            'user_id' => ['integer', 'required'],
            'description' => ['string', 'somtimes', 'nullable'],
            'remind_time' => ['integer', 'somtimes', 'nullable'],
            'id' => ['integer', 'sometimes', 'nullable'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        $id = (Reminder::max('id') ?? 0) + 1;
        $client_id = $current_user->currentClientId();
        $data['user_id'] = $client_id;

        ClientAggregate::retrieve($client_id)->createReminder($current_user->id ?? "Auto Generated", $data)->persist();

        return Reminder::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('reminders.create', Reminder::class);
    }

    public function asController(ActionRequest $request)
    {
        $reminder = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Reminder '{$reminder->name}' was created")->flash();

//        return Redirect::route('roles');
        return Redirect::route('reminder.edit', $reminder->id);
    }
}
