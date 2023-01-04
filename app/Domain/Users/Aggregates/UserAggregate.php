<?php

namespace App\Domain\Users\Aggregates;

use App\Domain\Clients\Projections\Client;
use App\Domain\Notifications\Events\NotificationCreated;
use App\Domain\Notifications\Events\NotificationDismissed;
use App\Domain\Reminders\Events\ReminderCreated;
use App\Domain\Reminders\Events\ReminderDeleted;
use App\Domain\Reminders\Events\ReminderTriggered;
use App\Domain\Reminders\Events\ReminderUpdated;
use App\Domain\Users\Aggregates\Traits\EndUserAggregate;
use App\Domain\Users\Events\AccessTokenGranted;
use App\Domain\Users\Events\FileUploaded;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserDeleted;
use App\Domain\Users\Events\UserImpersonatedAnother;
use App\Domain\Users\Events\UserObfuscated;
use App\Domain\Users\Events\UserPasswordUpdated;
use App\Domain\Users\Events\UserReceivedEmail;
use App\Domain\Users\Events\UserReceivedTextMsg;
use App\Domain\Users\Events\UserSetCustomCrudColumns;
use App\Domain\Users\Events\UsersImported;
use App\Domain\Users\Events\UserStoppedBeingImpersonated;
use App\Domain\Users\Events\UserStoppedImpersonatedAnother;
use App\Domain\Users\Events\UserUpdated;
use App\Domain\Users\Events\UserWasImpersonated;
use App\Domain\Users\Events\UserWelcomeEmailSent;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserAggregate extends AggregateRoot
{
    use EndUserAggregate;

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
    protected string $notes = '';
    protected string $start_date = '';
    protected string $end_date = '';
    protected string $termination_date = '';

    public function grantAccessToken(): static
    {
        $this->recordThat(new AccessTokenGranted());

        return $this;
    }

    public function updatePassword(string $password): static
    {
        $this->recordThat(new UserPasswordUpdated($password));

        return $this;
    }

    public function applyUserCreated(UserCreated $event): void
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

    public function applyUserUpdated(UserUpdated $event): void
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

    public function applyUserAddedToTeam(UserAddedToTeam $event): void
    {
        $this->teams[$event->team] = [
            'team_id' => $event->team,
            'client_id' => $event->client,
        ];
    }

    public function applyUserImpersonatedAnother(UserImpersonatedAnother $event): void
    {
        $this->activity_history[] = [
            'event' => 'user-started-impersonating',
            'user_impersonated' => $event->victim,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserStoppedImpersonatedAnother(UserStoppedImpersonatedAnother $event): void
    {
        $this->activity_history[] = [
            'event' => 'user-stopped-impersonating',
            'user_impersonated' => $event->victim,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserWasImpersonated(UserWasImpersonated $event): void
    {
        $this->activity_history[] = [
            'event' => 'user-was-impersonated',
            'impersonating_user' => $event->invader,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserStoppedBeingImpersonated(UserStoppedBeingImpersonated $event): void
    {
        $this->activity_history[] = [
            'event' => 'user-stopped-being-impersonated',
            'impersonating_user' => $event->invader,
            'date' => $event->createdAt(),
        ];
    }

    public function applyUserReceivedTextMsg(UserReceivedTextMsg $event): void
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

    public function applyUserReceivedEmail(UserReceivedEmail $event): void
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

    public function import(string $created_by_user_id, string $key, string $client): static
    {
        $this->recordThat(new UsersImported($this->uuid(), $created_by_user_id, $key, $client));

        return $this;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new UserCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new UserUpdated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new UserTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new UserRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new UserDeleted());

        return $this;
    }

    public function sendWelcomeEmail(): static
    {
        // @todo - logic to throw an exception if the user is active
        $this->recordThat(new UserWelcomeEmailSent($this->uuid()));

        return $this;
    }

    //TODO we need to obfuscate data in the aggregate a well (even before the point in time it was obfuscated)
    public function ObfuscateUser(): static
    {
        $this->recordThat(new UserObfuscated());

        return $this;
    }

    public function setCustomCrudColumns(string $table, array $field_ids): static
    {
        $this->recordThat(new UserSetCustomCrudColumns($table, $field_ids));

        return $this;
    }

    public function getTeams(): array
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

    public function activateUserImpersonationMode(string $victim_id): static
    {
        $this->recordThat(new UserImpersonatedAnother($this->uuid(), $victim_id));

        return $this;
    }

    public function deactivateUserImpersonationMode(string $liberated_id): static
    {
        $this->recordThat(new UserStoppedImpersonatedAnother($this->uuid(), $liberated_id));

        return $this;
    }

    public function activatePossessionMode(string $invader_id): static
    {
        $this->recordThat(new UserWasImpersonated($this->uuid(), $invader_id));

        return $this;
    }

    public function deactivatePossessionMode(string $coward_id): static
    {
        $this->recordThat(new UserStoppedBeingImpersonated($this->uuid(), $coward_id));

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getEmailAddress(): string
    {
        return $this->email;
    }

    public function getProperty(string $prop): mixed
    {
        switch ($prop) {
            case 'name':
                return $this->getName();

                break;

            default:
                return false;
        }
    }

    public function createNotification(array $data): static
    {
        $this->recordThat(new NotificationCreated($data));

        return $this;
    }

    public function dismissNotification(string $notification_id): static
    {
        $this->recordThat(new NotificationDismissed($notification_id));

        return $this;
    }

    public function createReminder(array $payload): static
    {
        $this->recordThat(new ReminderCreated($payload));

        return $this;
    }

    public function updateReminder(array $payload): static
    {
        $this->recordThat(new ReminderUpdated($payload));

        return $this;
    }

    public function deleteReminder(string $id): static
    {
        $this->recordThat(new ReminderDeleted($id));

        return $this;
    }

    public function triggerReminder(string $id): static
    {
        $this->recordThat(new ReminderTriggered($id));

        return $this;
    }

    public function uploadFile(array $payload): static
    {
        $this->recordThat(new FileUploaded($payload));

        return $this;
    }
}