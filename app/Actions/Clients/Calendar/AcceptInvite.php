<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarAttendee;
use App\Models\Calendar\CalendarEvent;
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

    public function handle($data, $current_user)
    {
        CalendarAggregate::retrieve($current_user->currentClientId())
            ->acceptCalendarEvent($current_user->id, $data)
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
            $request->user(),
        );

        Alert::success("Invitation Accepted!")->flash();

        return Redirect::back();
    }

}
