<?php

namespace App\Domain\Reminders\Actions;

use App\Domain\Users\UserAggregate;
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
            'id' => ['string', 'sometimes'],
        ];
    }

    public function handle($data, $current_user)
    {
        if (! is_null($current_user)) {
            $data['client_id'] = $current_user->client_id;
        }

        UserAggregate::retrieve($current_user->id)->deleteReminder($data['id'])->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Reminder deleted")->flash();

        return Redirect::back();
    }
}
