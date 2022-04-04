<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashCalendarEvent
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
        CalendarAggregate::retrieve($data['client_id'])->deleteCalendarEvent($user->id ?? "Auto Generated", $data['id'])->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.trash', CalendarEvent::class);
    }

    public function asController(Request $request, $id)
    {
        $calendar = CalendarEvent::findOrFail($id);

        $this->handle(
            $calendar->toArray()
        );

        Alert::success("Calendar Event '{$calendar->title}' sent to trash")->flash();

        return Redirect::back();
    }
}
