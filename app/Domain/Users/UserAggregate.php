<?php

namespace App\Domain\Users;

use App\Domain\Clients\Models\Client;
use App\Domain\Notifications\Events\NotificationCreated;
use App\Domain\Notifications\Events\NotificationDismissed;
use App\Domain\Reminders\Events\ReminderCreated;
use App\Domain\Reminders\Events\ReminderDeleted;
use App\Domain\Reminders\Events\ReminderTriggered;
use App\Domain\Reminders\Events\ReminderUpdated;
use App\Domain\Users\Events\AccessTokenGranted;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserDeleted;
use App\Domain\Users\Events\UserPasswordUpdated;
use App\Domain\Users\Events\UserRestored;
use App\Domain\Users\Events\UsersImported;
use App\Domain\Users\Events\UserTrashed;
use App\Domain\Users\Events\UserUpdated;
use App\StorableEvents\Users\Activity\Email\UserReceivedEmail;
use App\StorableEvents\Users\Activity\Impersonation\UserImpersonatedAnother;
use App\StorableEvents\Users\Activity\Impersonation\UserStoppedBeingImpersonated;
use App\StorableEvents\Users\Activity\Impersonation\UserStoppedImpersonatedAnother;
use App\StorableEvents\Users\Activity\Impersonation\UserWasImpersonated;
use App\StorableEvents\Users\Activity\SMS\UserReceivedTextMsg;
use App\StorableEvents\Users\UserAddedToTeam;
use App\StorableEvents\Users\UserSetCustomCrudColumns;
use App\StorableEvents\Users\WelcomeEmailSent;
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
        $this->recordThat(new AccessTokenGranted());

        return $this;
    }

    public function updatePassword(string $password)
    {
        $this->recordThat(new UserPasswordUpdated($password));

        return $this;
    }

    public function applyUserCreated(UserCreated $event)
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

    public function applyUserUpdated(UserUpdated $event)
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

    public function import(string $created_by_user_id, string $key, string $client)
    {
        $this->recordThat(new UsersImported($this->uuid(), $created_by_user_id, $key, $client));

        return $this;
    }

    public function create(array $payload)
    {
        $this->recordThat(new UserCreated($payload));

        return $this;
    }

    public function update(array $payload)
    {
        $this->recordThat(new UserUpdated($payload));

        return $this;
    }

    public function trash()
    {
        $this->recordThat(new UserTrashed());

        return $this;
    }

    public function restore()
    {
        $this->recordThat(new UserRestored());

        return $this;
    }

    public function delete()
    {
        $this->recordThat(new UserDeleted());

        return $this;
    }

    public function sendWelcomeEmail()
    {
        // @todo - logic to throw an exception if the user is active
        $this->recordThat(new WelcomeEmailSent($this->uuid()));

        return $this;
    }

    public function addToTeam(string $team_id, string $team_name)
    {
        if (array_key_exists($team_id, $this->teams)) {
            // @todo - make an exception to throw here that the user is already a member
        }

        $this->recordThat(new UserAddedToTeam($this->uuid(), $team_id, $team_name));

        return $this;
    }

    public function setCustomCrudColumns(string $table, array $field_ids)
    {
        $this->recordThat(new UserSetCustomCrudColumns($this->uuid(), $table, $field_ids));

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
        $this->recordThat(new NotificationCreated($data));

        return $this;
    }

    public function dismissNotification(string $id)
    {
        $this->recordThat(new NotificationDismissed($id));

        return $this;
    }

    public function createReminder(array $payload)
    {
        $this->recordThat(new ReminderCreated($payload));

        return $this;
    }

    public function updateReminder(array $payload)
    {
        $this->recordThat(new ReminderUpdated($payload));

        return $this;
    }

    public function deleteReminder(string $id)
    {
        $this->recordThat(new ReminderDeleted($id));

        return $this;
    }

    public function triggerReminder(string $id)
    {
        $this->recordThat(new ReminderTriggered($id));

        return $this;
    }
}
