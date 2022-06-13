<?php

namespace App\Aggregates\Users;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use App\StorableEvents\Clients\Tasks\TaskDeleted;
use App\StorableEvents\Clients\Tasks\TaskMarkedComplete;
use App\StorableEvents\Clients\Tasks\TaskMarkedIncomplete;
use App\StorableEvents\Clients\Tasks\TaskRestored;
use App\StorableEvents\Clients\Tasks\TaskTrashed;
use App\StorableEvents\Clients\Tasks\TaskUpdated;
use App\StorableEvents\Users\Activity\API\AccessTokenGranted;
use App\StorableEvents\Users\Activity\Email\UserReceivedEmail;
use App\StorableEvents\Users\Activity\Impersonation\UserImpersonatedAnother;
use App\StorableEvents\Users\Activity\Impersonation\UserStoppedBeingImpersonated;
use App\StorableEvents\Users\Activity\Impersonation\UserStoppedImpersonatedAnother;
use App\StorableEvents\Users\Activity\Impersonation\UserWasImpersonated;
use App\StorableEvents\Users\Activity\SMS\UserReceivedTextMsg;
use App\StorableEvents\Users\Notifications\NotificationCreated;
use App\StorableEvents\Users\Notifications\NotificationDismissed;
use App\StorableEvents\Users\Reminder\ReminderCreated;
use App\StorableEvents\Users\Reminder\ReminderDeleted;
use App\StorableEvents\Users\Reminder\ReminderTriggered;
use App\StorableEvents\Users\Reminder\ReminderUpdated;
use App\StorableEvents\Users\UserAddedToTeam;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    protected $client_id = '';
    protected $teams = [];
    protected array $activity_history = [];
    protected $phone_number = '';
    protected string $name = '';
    protected string $first_name = '';
    protected string $last_name = '';
    protected string $email = '';
    protected string $alt_email = '';
    protected string $address1 = '';
    protected string $address2 = '';
    protected string $city = '';
    protected string $state = '';
    protected string $zip = '';
    protected string $job_title = '';
    protected string $notes = '';
    protected string $start_date = '';
    protected string $end_date = '';
    protected string $termination_date = '';

    public function grantAccessToken()
    {
        $this->recordThat(new AccessTokenGranted($this->uuid()));

        return $this;
    }

    public function applyNewUser(\App\Domain\Users\Events\UserCreated $event)
    {
        if (array_key_exists('name', $event->payload)) {
            $this->name = $event->payload['name'];
        }

        if (array_key_exists('phone', $event->payload)) {
            $this->phone_number = $event->payload['phone'];
        }

        if (array_key_exists('email', $event->payload)) {
            $this->email = $event->payload['email'];
        }


        // @todo - put something useful here
    }

    public function applyUserUpdated(\App\Domain\Users\Events\UserUpdated $event)
    {
        if (array_key_exists('name', $event->payload)) {
            $this->name = $event->payload['name'];
        }

        if (array_key_exists('phone', $event->payload)) {
            $this->phone_number = $event->payload['phone'];
        }

        if (array_key_exists('email', $event->payload)) {
            $this->email = $event->payload['email'];
        }

        // @todo - put something useful here
    }

    public function applyUserAddedToTeam(UserAddedToTeam $event)
    {
        $this->teams[$event->team] = [
            'team_id' => $event->team,
            'client_id' => $event->client,
        ];
    }

    public function applyUserImpersonatedAnother(UserImpersonatedAnother $event)
    {
        $this->activity_history[] = [
            'event' => 'user-started-impersonating',
            'user_impersonated' => $event->victim,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserStoppedImpersonatedAnother(UserStoppedImpersonatedAnother $event)
    {
        $this->activity_history[] = [
            'event' => 'user-stopped-impersonating',
            'user_impersonated' => $event->victim,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserWasImpersonated(UserWasImpersonated $event)
    {
        $this->activity_history[] = [
            'event' => 'user-was-impersonated',
            'impersonating_user' => $event->invader,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserStoppedBeingImpersonated(UserStoppedBeingImpersonated $event)
    {
        $this->activity_history[] = [
            'event' => 'user-stopped-being-impersonated',
            'impersonating_user' => $event->invader,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserReceivedTextMsg(UserReceivedTextMsg $event)
    {
        $this->activity_history[] = [
            'event' => 'sms-transmission',
            'details' => [
                'user_id' => $event->user,
                'template_id' => $event->template,
                'misc' => [
                    'response' => $event->response,
                    'client' => $event->client ?? null,
                ],
            ],
        ];
    }

    public function applyUserReceivedEmail(UserReceivedEmail $event)
    {
        $this->activity_history[] = [
            'event' => 'email-transmission',
            'details' => [
                'user_id' => $event->user,
                'subject' => $event->subject,
                'template_id' => $event->template,
                'misc' => [
                    'response' => $event->response,
                    'client' => $event->client ?? null,
                ],
            ],
        ];
    }

    public function createTask(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new TaskCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function deleteTask(string $deleted_by_user_id, TaskDeleted $event)
    {
        $this->recordThat(new TaskDeleted($this->uuid(), $deleted_by_user_id, $event));

        return $this;
    }

    public function restoreTask(string $restored_by_user_id, TaskRestored $event)
    {
        $this->recordThat(new TaskRestored($this->uuid(), $restored_by_user_id, $event));

        return $this;
    }

    public function trashTask(string $deleted_by_user_id, TaskTrashed $event)
    {
        $this->recordThat(new TaskTrashed($this->uuid(), $deleted_by_user_id, $event));

        return $this;
    }

    public function updateTask(string $updated_by_user_id, TaskUpdated $event)
    {
        $this->recordThat(new TaskUpdated($this->uuid()), $updated_by_user_id, $event);

        return $this;
    }

    public function markTaskAsComplete(string $updated_by_user_id, TaskMarkedComplete $event)
    {
        $this->recordThat(new TaskMarkedComplete($this->uuid()), $updated_by_user_id, $event);

        return $this;
    }

    public function markTaskAsIncomplete(string $updated_by_user_id, TaskMarkedIncomplete $event)
    {
        $this->recordThat(new TaskMarkedIncomplete($this->uuid()), $updated_by_user_id, $event);

        return $this;
    }

    public function logClientSMSActivity(string $template_id, string $response, string $client_id = null)
    {
        $this->recordThat(new UserReceivedTextMsg($this->uuid(), $template_id, $response, $client_id));

        return $this;
    }

    public function logClientEmailActivity(string $subject, string $template_id, array $response, string $client_id = null)
    {
        $this->recordThat(new UserReceivedEmail($this->uuid(), $subject, $template_id, $response, $client_id));

        return $this;
    }

    public function getTeams()
    {
        $results = $this->teams;

        $client = new Client();
        foreach ($results as $idx => $team) {
            if (! is_null($team['client_id']) && (($client->id ?? null) != $team['client_id'])) {
                $client = $client->find($team['client_id']);
            }

            $client_name = $client->name ?? 'GymRevenue';
            $results[$idx]['client_name'] = $client_name;
        }

        return $results;
    }

    public function activateUserImpersonationMode(string $victim_id)
    {
        $this->recordThat(new UserImpersonatedAnother($this->uuid(), $victim_id));

        return $this;
    }

    public function deactivateUserImpersonationMode(string $liberated_id)
    {
        $this->recordThat(new UserStoppedImpersonatedAnother($this->uuid(), $liberated_id));

        return $this;
    }

    public function activatePossessionMode(string $invader_id)
    {
        $this->recordThat(new UserWasImpersonated($this->uuid(), $invader_id));

        return $this;
    }

    public function deactivatePossessionMode(string $coward_id)
    {
        $this->recordThat(new UserStoppedBeingImpersonated($this->uuid(), $coward_id));

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    public function getEmailAddress()
    {
        return $this->email;
    }

    public function getProperty(string $prop)
    {
        switch ($prop) {
            case 'name':
                return $this->getName();

                break;

            default:
                return false;
        }
    }

    public function createNotification(array $data)
    {
        $this->recordThat(new NotificationCreated($this->uuid(), $data));

        return $this;
    }

    public function dismissNotification(string $id)
    {
        $this->recordThat(new NotificationDismissed($this->uuid(), $id));

        return $this;
    }

    public function createReminder(string $user_id, array $payload)
    {
        $this->recordThat(new ReminderCreated($user_id, $payload));

        return $this;
    }

    public function updateReminder(string $user_id, array $payload)
    {
        $this->recordThat(new ReminderUpdated($user_id, $payload));

        return $this;
    }

    public function deleteReminder(string $user_id, string $id)
    {
        $this->recordThat(new ReminderDeleted($user_id, $id));

        return $this;
    }

    public function triggerReminder(string $id)
    {
        $this->recordThat(new ReminderTriggered($this->uuid(), $id));

        return $this;
    }
}
