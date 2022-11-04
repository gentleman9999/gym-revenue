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
            $end_user = (new EndUser())->writeable();
            $fillable_data = array_filter($event->payload, function ($key) use ($end_user) {
                return in_array($key, $end_user->getFillable());
            }, ARRAY_FILTER_USE_KEY);
            $end_user->id = $event->aggregateRootUuid();
            $end_user->client_id = $event->payload['client_id'];
            $end_user->email = $event->payload['email'];
            $end_user->fill($fillable_data);

            $end_user->save();

            $this->createOrUpdateEndUserNotes($event, $end_user);

            //ON END USER CREATION, END USER IS AUTOMATICALLY A LEAD NO MATTER WHAT
            $end_user = (new Lead())->writeable();
            $fillable_data = array_filter($event->payload, function ($key) use ($end_user) {
                return in_array($key, $end_user->getFillable());
            }, ARRAY_FILTER_USE_KEY);
            $end_user->id = $event->aggregateRootUuid();
            $end_user->client_id = $event->payload['client_id'];
            $end_user->email = $event->payload['email'];
            $end_user->fill($fillable_data);

            $end_user->save();
        });
    }

    public function onEndUserUpdated(EndUserUpdated $event): void
    {
        $end_user = EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
        if (array_key_exists('email', $event->payload)) {
            $end_user->email = $event->payload['email'];
        }
        $end_user->fill($event->payload);
        $end_user->save();

        $this->createOrUpdateEndUserNotes($event, $end_user);
    }

    public function onEndUserTrashed(EndUserTrashed $event): void
    {
        DB::transaction(function () use ($event) {
            $end_user = EndUser::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
            $end_user->delete();

            $potentialLead = Lead::withTrashed()->find($event->aggregateRootUuid()); //check to see if in leads table
            $potentialCustomer = Customer::withTrashed()->find($event->aggregateRootUuid()); //check to see if in customers table
            $potentialMember = Member::withTrashed()->find($event->aggregateRootUuid()); //check to see if in members table

            if ($potentialLead) {
                Lead::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
            }
            if ($potentialCustomer) {
                Customer::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
            }
            if ($potentialMember) {
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
}
