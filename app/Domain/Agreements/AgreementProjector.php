<?php

declare(strict_types=1);

namespace App\Domain\Agreements;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
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
            $agreement->save();

            $end_user_id = $event->payload['end_user_id'];

            $end_user = EndUser::find($end_user_id); //Find Current EndUser information

            $potential_lead = Lead::withTrashed()->find($end_user_id); //check to see if in leads table
            $potential_customer = Customer::withTrashed()->find($end_user_id); //check to see if in customers table
            $potential_member = Member::withTrashed()->find($end_user_id); //check to see if in members table

            if ($potential_lead) {
                $potential_lead->writeable()->forceDelete();
            }
            if ($potential_customer) {
                $potential_customer->writeable()->forceDelete();
            }
            if ($potential_member) {
                $potential_member->writeable()->forceDelete();
            }

            //Fetching all agreement with category of end user
            $agreements = Agreement::with('categoryById')->whereEndUserId($end_user_id)->get();

            //Checking if any agreement is of membership category
            $is_membership_agreement = false;
            foreach ($agreements as $agreement) {
                if ($agreement->categoryById && $agreement->categoryById['name'] === AgreementCategory::NAME_MEMBERSHIP) {
                    $is_membership_agreement = true;
                }
            }

            $active = $event->payload['active'];

            if ($active && ! $is_membership_agreement) { //Turn lead into customer
                $customer = (new Customer())->writeable();
                $this->fill($end_user, $customer);
            }

            if ($active && $is_membership_agreement) { //Turn lead into member
                $member = (new Member())->writeable();
                $this->fill($end_user, $member);
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
     * @param $end_user
     * @param $type
     * @return void
     */
    public function fill(EndUser $end_user, Customer|Member $type): void
    {
        $fillables = $end_user->getFillable();
        $fillable_data = array_filter($end_user->toArray(), function ($key) use ($fillables) {
            return in_array($key, $fillables);
        }, ARRAY_FILTER_USE_KEY);
        $type->id = $end_user->id;
        $type->client_id = $end_user->client_id;
        $type->email = $end_user->email;
        $type->fill($fillable_data);
        $type->save();
    }
}
