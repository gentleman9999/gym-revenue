<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarEvent;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreCalendarEvent
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($data, $user=null)
    {
        CalendarAggregate::retrieve($user->id ?? "Auto Generated", $data['client_id'])->restoreLocation()->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.restore', CalendarEvent::class);
    }

    public function asController(Request $request, $id)
    {
        $calendar = CalendarEvent::withTrashed()->findOrFail($id);

        $this->handle(
            $calendar->toArray(),
            $request->user()
        );

        Alert::success("Calendar Event '{$calendar->title}' restored.")->flash();

        return Redirect::back();
    }
}
