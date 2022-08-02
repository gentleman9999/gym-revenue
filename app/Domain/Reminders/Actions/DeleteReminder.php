<?php

namespace App\Domain\Reminders\Actions;

use App\Domain\Users\UserAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteReminder
{
    use AsAction;

    public function handle(Reminder $reminder): Reminder
    {
        UserAggregate::retrieve($reminder->user_id)->deleteReminder($reminder->id)->persist();

        return $reminder;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    //TODO: implement real authorization
    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request, Reminder $reminder): RedirectResponse
    {
        $this->handle(
            $reminder,
        );

        Alert::success("Reminder deleted")->flash();

        return Redirect::back();
    }
}
