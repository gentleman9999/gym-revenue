<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Helpers\Uuid;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Endusers\Lead;
use App\Models\User;
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
            'name' =>['required', 'string','max:50'],
            'phone' => ['string', 'nullable'],
        ];
    }

    public function handle($data, $user = null)
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
        $current_user = $request->user();
        return $current_user->can('calendar.create', CalendarEvent::class);
    }

    public function asController(ActionRequest $request)
    {
        $attendee = $this->handle(
            $request->validated(),
            $request->user()
        );

        Alert::success("Attendee '{$attendee->name}' was invited to the scheduled event.")->flash();

        return Redirect::back();
    }

}
