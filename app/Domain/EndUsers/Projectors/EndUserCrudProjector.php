<?php

namespace App\Domain\EndUsers\Projectors;

use App\Domain\EndUsers\Customers\Projections\Customer;
use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Events\EndUserDeleted;
use App\Domain\EndUsers\Events\EndUserProfilePictureMoved;
use App\Domain\EndUsers\Events\EndUserRestored;
use App\Domain\EndUsers\Events\EndUserTrashed;
use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EndUserCrudProjector extends Projector
{
    public function onEndUserCreated(EndUserCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $end_user = $this->createEndUserEntity($event, (string) EndUser::class);

            $this->setEndUserEntity($event, $end_user);

            $this->createOrUpdateEndUserNotes($event, $end_user);

            //ON END USER CREATION, END USER IS AUTOMATICALLY A LEAD NO MATTER WHAT
            $end_user = $this->createEndUserEntity($event, (string) Lead::class);

            $this->setEndUserEntity($event, $end_user);
        });
    }

    public function onEndUserUpdated(EndUserUpdated $event): void
    {
        DB::transaction(function () use ($event) {
            $end_user = EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
            $this->setEndUserEntity($event, $end_user);

            $this->createOrUpdateEndUserNotes($event, $end_user);

            if ($this->isPotentialLead($event)) {
                $end_user = Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
                $this->setEndUserEntity($event, $end_user);
            }
            if ($this->isPotentialCustomer($event)) {
                $end_user = Customer::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
                $this->setEndUserEntity($event, $end_user);
            }
            if ($this->isPotentialMember($event)) {
                $end_user = Member::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
                $this->setEndUserEntity($event, $end_user);
            }
        });
    }

    public function onEndUserTrashed(EndUserTrashed $event): void
    {
        DB::transaction(function () use ($event) {
            $end_user = EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
            $end_user->delete();

            if ($this->isPotentialLead($event)) {
                Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
            }
            if ($this->isPotentialCustomer($event)) {
                Customer::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
            }
            if ($this->isPotentialMember($event)) {
                Member::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
            }
        });
    }

    public function onEndUserRestored(EndUserRestored $event): void
    {
        EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onEndUserDeleted(EndUserDeleted $event): void
    {
        EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onEndUserProfilePictureMoved(EndUserProfilePictureMoved $event): void
    {
        EndUser::findOrFail($event->aggregateRootUuid())->writeable()->update(['profile_picture' => $event->file]);
    }

    public function onEndUserUpdatedCommunicationPreferences(EndUserUpdatedCommunicationPreferences $event): void
    {
        $end_user = EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
        $end_user->forceFill(['unsubscribed_email' => $event->email, 'unsubscribed_sms' => $event->sms])->save();
    }

    protected function createOrUpdateEndUserNotes($event, EndUser $end_user): void
    {
        $notes = $event->payload['notes'] ?? false;
        if ($notes && $notes['title'] ?? false) {
            Note::create([
                'entity_id' => $end_user->id,
                'entity_type' => ($end_user::getDetailsModel())::class,
                'title' => $notes['title'],
                'note' => $notes['note'],
                'created_by_user_id' => $event->modifiedBy(),
            ]);
        }
    }

    /**
     * Check to see if enduser is in leads table
     */
    protected function isPotentialLead($event): bool
    {
        return count(Lead::withTrashed()->whereId($event->aggregateRootUuid())->get());
    }

    /**
     * Check to see if enduser is in customers table
     */
    protected function isPotentialCustomer($event): bool
    {
        return count(Customer::withTrashed()->whereId($event->aggregateRootUuid())->get());
    }

    /**
     * Check to see if enduser is in members table
     */
    protected function isPotentialMember($event): bool
    {
        return count(Member::withTrashed()->whereId($event->aggregateRootUuid())->get());
    }

    protected function createEndUserEntity($event, string $entity): EndUser
    {
        $end_user = null;

        if ($entity == (string) EndUser::class) {
            $end_user = (new EndUser())->writeable();
        } elseif ($entity == (string) Lead::class) {
            $end_user = (new Lead())->writeable();
        } elseif ($entity == (string) Member::class) {
            $end_user = (new Member())->writeable();
        } else {
            $end_user = (new Customer())->writeable();
        }

        $end_user->id = $event->aggregateRootUuid();
        $end_user->client_id = $event->payload['client_id'];

        return $end_user;
    }

    protected function setEndUserEntity($event, EndUser $end_user): void
    {
        $end_user->email = $event->payload['email'] ?? $end_user->email;
        $end_user->fill($event->payload);
        $end_user->save();
    }
}
