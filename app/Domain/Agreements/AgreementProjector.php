<?php

namespace App\Domain\Agreements;

use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementDeleted;
use App\Domain\Agreements\Events\AgreementRestored;
use App\Domain\Agreements\Events\AgreementTrashed;
use App\Domain\Agreements\Events\AgreementUpdated;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\EndUsers\Customers\Projections\Customer;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AgreementProjector extends Projector
{
    public function onStartingEventReplay()
    {
        Agreement::truncate();
    }

    public function onAgreementCreated(AgreementCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $agreement = (new Agreement())->writeable();
            $agreement->fill($event->payload);
            $agreement->id = $event->aggregateRootUuid();
            $agreement->client_id = $event->payload['client_id'];
            $agreement->save();

            $endUser = EndUser::find($event->payload['end_user_id']); //Find Current EndUser information
            $potentialLead = Lead::withTrashed()->find($event->payload['end_user_id']); //check to see if in leads table
            $potentialCustomer = Customer::withTrashed()->find($event->payload['end_user_id']); //check to see if in customers table
            $potentialMember = Member::withTrashed()->find($event->payload['end_user_id']); //check to see if in members table

            if ($potentialLead) {
                Lead::withTrashed()->findOrFail($event->payload['end_user_id'])->writeable()->forceDelete();
            }
            if ($potentialCustomer) {
                Customer::withTrashed()->findOrFail($event->payload['end_user_id'])->writeable()->forceDelete();
            }
            if ($potentialMember) {
                Member::withTrashed()->findOrFail($event->payload['end_user_id'])->writeable()->forceDelete();
            }

            if (! $event->payload['active']) { //Turn lead into customer
                $customer = (new Customer())->writeable();
                $this->fill($endUser, $customer);
            }

            if ($event->payload['active']) { //Turn lead into member
                $member = (new Member())->writeable();
                $this->fill($endUser, $member);
            }
        });
    }

    public function onAgreementDeleted(AgreementDeleted $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onAgreementRestored(AgreementRestored $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAgreementTrashed(AgreementTrashed $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAgreementUpdated(AgreementUpdated $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }

    /**
     * @param $endUser
     * @param $type
     * @return void
     */
    public function fill($endUser, $type): void
    {
        $fillable_data = array_filter($endUser->toArray(), function ($key) use ($endUser) {
            return in_array($key, $endUser->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $type->id = $endUser->id;
        $type->client_id = $endUser->client_id;
        $type->email = $endUser->email;
        $type->fill($fillable_data);
        $type->save();
    }
}
