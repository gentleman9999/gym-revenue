<?php

namespace App\Aggregates\Users;

use App\Models\Clients\Client;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use App\StorableEvents\Clients\Tasks\TaskDeleted;
use App\StorableEvents\Clients\Tasks\TaskMarkedComplete;
use App\StorableEvents\Clients\Tasks\TaskMarkedIncomplete;
use App\StorableEvents\Clients\Tasks\TaskRestored;
use App\StorableEvents\Clients\Tasks\TaskTrashed;
use App\StorableEvents\Clients\Tasks\TaskUpdated;
use App\StorableEvents\Users\Activity\Impersonation\UserImpersonatedAnother;
use App\StorableEvents\Users\Activity\Impersonation\UserStoppedBeingImpersonated;
use App\StorableEvents\Users\Activity\Impersonation\UserStoppedImpersonatedAnother;
use App\StorableEvents\Users\Activity\Impersonation\UserWasImpersonated;
use App\StorableEvents\Users\Activity\Email\UserReceivedEmail;
use App\StorableEvents\Users\Activity\SMS\UserReceivedTextMsg;
use App\StorableEvents\Users\Notifications\NotificationCreated;
use App\StorableEvents\Users\Notifications\NotificationDismissed;
use App\StorableEvents\Users\UserAddedToTeam;
use App\StorableEvents\Users\UserCreated;
use App\StorableEvents\Users\UserDeleted;
use App\StorableEvents\Users\UserSetCustomCrudColumns;
use App\StorableEvents\Users\UserUpdated;
use App\StorableEvents\Users\WelcomeEmailSent;
use Illuminate\Database\Eloquent\Model;
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

    public function applyNewUser(UserCreated $event)
    {
        if(array_key_exists('name', $event->payload))
        {
            $this->name = $event->payload['name'];
        }

        if(array_key_exists('phone', $event->payload))
        {
            $this->phone_number = $event->payload['phone'];
        }

        if(array_key_exists('email', $event->payload))
        {
            $this->email = $event->payload['email'];
        }


        // @todo - put something useful here
    }

    public function applyUserUpdated(UserUpdated $event)
    {
        if(array_key_exists('name', $event->payload))
        {
            $this->name = $event->payload['name'];
        }

        if(array_key_exists('phone', $event->payload))
        {
            $this->phone_number = $event->payload['phone'];
        }

        if(array_key_exists('email', $event->payload))
        {
            $this->email = $event->payload['email'];
        }

        // @todo - put something useful here
    }

    public function applyUserAddedToTeam(UserAddedToTeam $event)
    {
        $this->teams[$event->team] = [
            'team_id' => $event->team,
            'team_name' => $event->name,
            'client_id' => $event->client
        ];
    }

    public function applyUserImpersonatedAnother(UserImpersonatedAnother $event)
    {
        $this->activity_history[] = [
            'event' => 'user-started-impersonating',
            'user_impersonated' => $event->victim,
            'date' => $event->date,
        ];
    }
    public function applyUserStoppedImpersonatedAnother(UserStoppedImpersonatedAnother $event)
    {
        $this->activity_history[] = [
            'event' => 'user-stopped-impersonating',
            'user_impersonated' => $event->victim,
            'date' => $event->date,
        ];
    }

    public function applyUserWasImpersonated(UserWasImpersonated $event)
    {
        $this->activity_history[] = [
            'event' => 'user-was-impersonated',
            'impersonating_user' => $event->invader,
            'date' => $event->date,
        ];
    }
    public function applyUserStoppedBeingImpersonated(UserStoppedBeingImpersonated $event)
    {
        $this->activity_history[] = [
            'event' => 'user-stopped-being-impersonated',
            'impersonating_user' => $event->invader,
            'date' => $event->date,
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
                    'client' => $event->client ?? null
                ]
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
                    'client' => $event->client ?? null
                ]
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

    public function restoreTask( $restored_by_user_id, TaskRestored $event)
    {
        $this->recordThat(new TaskRestored($this->uuid(), $restored_by_user_id, $event));
        return $this;

    }
    public function trashTask($deleted_by_user_id, TaskTrashed $event)
    {
        $this->recordThat(new TaskTrashed($this->uuid(), $deleted_by_user_id, $event));
        return $this;
    }
    public function updateTask($updated_by_user_id, TaskUpdated $event)
    {
        $this->recordThat(new TaskUpdated($this->uuid()), $updated_by_user_id, $event);
        return $this;
    }
    public function markTaskAsComplete($updated_by_user_id, TaskMarkedComplete $event)
    {
        $this->recordThat(new TaskMarkedComplete($this->uuid()), $updated_by_user_id, $event);
        return $this;
    }
    public function markTaskAsIncomplete($updated_by_user_id, TaskMarkedIncomplete $event)
    {
        $this->recordThat(new TaskMarkedIncomplete($this->uuid()), $updated_by_user_id, $event);
        return $this;
    }




    public function createUser(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new UserCreated($this->uuid(), $created_by_user_id, $payload));
        return $this;
    }

    public function deleteUser(string $deleted_by_user_id, array $payload)
    {
        $this->recordThat(new UserDeleted($this->uuid(), $deleted_by_user_id, $payload));
        return $this;
    }

    public function updateUser(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new UserUpdated($this->uuid(), $updated_by_user_id, $payload));
        return $this;
    }

    public function sendWelcomeEmail()
    {
        // @todo - logic to throw an exception if the user is active
        $this->recordThat(new WelcomeEmailSent($this->uuid()));
        return $this;
    }

    public function addUserToTeam(string $team_id, $team_name, $client_id = null)
    {
        if(array_key_exists($team_id, $this->teams))
        {
            // @todo - make an exception to throw here that the user is already a member
        }

        $this->recordThat(new UserAddedToTeam($this->uuid(), $team_id, $team_name, $client_id));

        return $this;
    }

    public function setCustomCrudColumns(string $table, array $field_ids)
    {
        $this->recordThat(new UserSetCustomCrudColumns($this->uuid(),$table, $field_ids));
        return $this;
    }

    public function logClientSMSActivity($template_id, $response, $client_id = null)
    {
        $this->recordThat(new UserReceivedTextMsg($this->uuid(), $template_id, $response, $client_id));
        return $this;
    }

    public function logClientEmailActivity($subject, $template_id, $response, $client_id = null)
    {
        $this->recordThat(new UserReceivedEmail($this->uuid(), $subject, $template_id, $response, $client_id));
        return $this;
    }

    public function getTeams()
    {
        $results = $this->teams;

        $client = new Client();
        foreach($results as $idx => $team)
        {
            if(!is_null($team['client_id']) && (($client->id ?? null) != $team['client_id']))
            {
                $client = $client->find($team['client_id']);
            }

            $client_name = $client->name ?? 'GymRevenue';
            $results[$idx]['client_name'] = $client_name;
        }

        return $results;
    }

    public function  activateUserImpersonationMode($victim_id)
    {
        $this->recordThat(new UserImpersonatedAnother($this->uuid(), $victim_id, date('Y-m-d H:i:s')));
        return $this;
    }

    public function  deactivateUserImpersonationMode($liberated_id)
    {
        $this->recordThat(new UserStoppedImpersonatedAnother($this->uuid(), $liberated_id, date('Y-m-d H:i:s')));
        return $this;
    }

    public function activatePossessionMode($invader_id)
    {
        $this->recordThat(new UserWasImpersonated($this->uuid(), $invader_id, date('Y-m-d H:i:s')));
        return $this;
    }

    public function deactivatePossessionMode($coward_id)
    {
        $this->recordThat(new UserStoppedBeingImpersonated($this->uuid(), $coward_id, date('Y-m-d H:i:s')));
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
        switch($prop)
        {
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
}
