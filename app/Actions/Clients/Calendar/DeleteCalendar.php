<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\Clients\Location;
use Bouncer;
use App\Actions\Fortify\PasswordValidationRules;
use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteCalendar
{
    use PasswordValidationRules, AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($data)
    {
        CalendarAggregate::retrieve($data['client_id'])->deleteCalendarEvent($data['id'])->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(Request $request, $id)
    {
        $calendar = Location::findOrFail($id);

        $this->handle(
            $calendar->toArray()
        );

        Alert::success("Calendar Event '{$calendar->name}' was deleted")->flash();

        return Redirect::back();
    }
}
