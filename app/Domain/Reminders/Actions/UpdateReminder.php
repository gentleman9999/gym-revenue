<?php

namespace App\Domain\Reminders\Actions;

use App\Domain\Reminders\Reminder;
use App\Domain\Users\UserAggregate;
use App\Http\Middleware\InjectClientId;
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

    public function handle(Reminder $reminder, array $data)
    {
        UserAggregate::retrieve($reminder->user_id)->updateReminder($data)->persist();

        return $reminder->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request, Reminder $reminder)
    {
        $reminder = $this->handle(
            $reminder,
            $request->validated(),
        );

        Alert::success("Reminder '{$reminder->name}' was updated")->flash();

        return Redirect::back();
    }
}
