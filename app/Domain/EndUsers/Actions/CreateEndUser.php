<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\CalendarEvents\Actions\CreateCalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;
use App\Support\Uuid;
use Carbon\Carbon;

abstract class CreateEndUser extends BaseEndUserAction
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'max:50'],
            'middle_name' => [],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'primary_phone' => ['required', 'string', 'min:10'],
            'alternate_phone' => ['sometimes', 'nullable', 'string', 'min:10'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'client_id' => 'required',
            'profile_picture' => 'sometimes',
            'profile_picture.uuid' => 'sometimes|required',
            'profile_picture.key' => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket' => 'sometimes|required',
            'gender' => 'sometimes|required',
            'date_of_birth' => 'sometimes|required',
            'opportunity' => 'sometimes|required',
            'owner_user_id' => 'sometimes|required|exists:users,id',
            'notes' => 'nullable|array',
        ];
    }

    public function handle(array $data, ?User $user = null): EndUser
    {
        $id = Uuid::new();
        $data['agreement_number'] = floor(time() - 99999999);

        ($this->getAggregate())::retrieve($id)->create($data)->persist();

        if (array_key_exists('lead_type_id', $data) || array_key_exists('agreement_number', $data)) {
            $leadAttendees = null;
            $memberAttendees = null;
            if (Lead::whereId($id)->exists()) {
                $leadAttendees = [$id];
            } else {
                $memberAttendees = [$id];
            }

            $drips = DripCampaign::whereStatus('ACTIVE')->get();
            if ($drips) {
                $client_id = $data['client_id'];
                foreach ($drips as $drip) {
                    $dripDays = DripCampaignDay::all()
                        ->where('drip_campaign_id', '=', $drip['id'])->toArray();
                    $event_type_id = CalendarEventType::where('type', '=', 'Task')->first()->id;
                    foreach ($dripDays as $day) {
                        $datetime = Carbon::now()->addDays($day['day_of_campaign']);
                        if ($day['call_template_id']) {
                            $task = [
                                'title' => 'Call Script Task for ' . $data['first_name'] . ' ' . $data['last_name'] . ' Drip Campaign',
                                'description' => 'Use the Call script Template for ' . $data['first_name'] . ' ' . $data['last_name'] . ' Drip Campaign. ',
                                'full_day_event' => false,
                                'start' => $datetime,
                                'due_at' => $datetime,
                                'end' => $datetime->addHour(),
                                'event_type_id' => $event_type_id,
                                'owner_id' => $user->id,
                                'user_attendees' => [$user->id],
                                'client_id' => $client_id,
                                'lead_attendees' => $leadAttendees,
                                'member_attendees' => $memberAttendees,
                                'location_id' => '',
                                'editable' => false,
                                'call_task' => true,
                            ];
                            CreateCalendarEvent::run($task);
                        }
                    }
                }
            }
        }

        return ($this->getModel())::findOrFail($id);
    }
}
