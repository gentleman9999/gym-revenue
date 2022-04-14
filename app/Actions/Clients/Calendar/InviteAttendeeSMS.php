<?php

namespace App\Actions\Clients\Calendar;


use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class InviteAttendeeSMS
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
        ];
    }

    public function handle($data)
    {
        $test = $data;
/*
        CalendarAggregate::retrieve($data['client_id'])
            ->inviteCalendarAttendee($user->id ?? "Auto Generated", $data)
            ->persist();
*/
        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $attendee = $this->handle(
            $request->validated()
        );

        Alert::success("Attendee '{$attendee->name}' was invited to the scheduled event.")->flash();

        return Redirect::back();
    }

}
