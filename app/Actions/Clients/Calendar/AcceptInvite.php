<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class AcceptInvite
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
            'attendeeData' => ['required'],
        ];
    }

    public function handle($data)
    {
        CalendarAggregate::retrieve($data['attendeeData']['event']['client_id'])
            ->acceptCalendarEvent($data['attendeeData']['entity_id'], $data)
            ->persist();

        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $attendee = $this->handle(
            $request->validated(),
        );

        Alert::success("Invitation Accepted!")->flash();

        return Redirect::back();
    }
}
