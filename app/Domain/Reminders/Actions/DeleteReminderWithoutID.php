<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Actions;

use App\Domain\Reminders\Reminder;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Http\Middleware\InjectClientId;
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
    public function rules(): array
    {
        return [
            'event_id' => ['string', 'required'],
            'entity_type' => ['string', 'required'],
            'user_id' => ['int', 'required'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        if ($current_user !== null) {
            $data['client_id'] = $current_user->client_id;
        }

        $reminder = Reminder::whereEntityType($data['entity_type'])->whereEntityId($data['entity_id'])->whereUserId($data['user_id'])->first();
        if ($reminder === null) {
            return true;
        } else {
            $id = $reminder->id;

            UserAggregate::retrieve($data['user_id'])->deleteReminder($id)->persist();

            return true;
        }
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    //TODO: implement real authorization
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
