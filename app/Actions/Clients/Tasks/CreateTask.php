<?php

namespace App\Actions\Clients\Tasks;

//use App\Aggregates\Clients\CalendarAggregate;
use App\Aggregates\Users\UserAggregate;

use App\Helpers\Uuid;
use App\Models\User;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class CreateTask
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
            'title' =>['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'user_id' => ['sometimes'],
            'due_at' => ['sometimes'],
            'completed_at' => ['sometimes'],
        ];
    }

    public function handle($data, $user = null)
    {
        $id = Uuid::new();
        $data['id'] = $id;



        if(isset($user->id))
//            $data['user_attendees'][] = $user->id; //If you make the event, you're automatically an attendee.

        if(!is_null($data['title'])) {
                $user = User::whereId($user)->select('id', 'name', 'email')->first();
                if($user) {
                    UserAggregate::retrieve($data['client_id'])
                        ->addTaskCreated($user->id ?? "Auto Generated",
                            [
                        'entity_type' => User::class,
                        'user_id' => $user->id,
                        'due_at' => $user,
                        'title' => $id,
                        'description' => 'Invitation Pending'
                        ])->persist();
                }
            }
        }



 //       unset($data['user_attendees']);
 //       unset($data['lead_attendees']);


        UserAggregate::retrieve($data['client_id'])
            ->createTask($user->id ?? "Auto Generated", $data)
            ->persist();

        return UserAggregate::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('task.create', TaskCreated::class);
    }

    public function asController(ActionRequest $request)
    {

        $calendar = $this->handle(
            $request->validated(),
            $request->user()
        );

        Alert::success("Calendar Event '{$calendar->title}' was created")->flash();

        return Redirect::back();
    }

}
