<?php

declare(strict_types=1);

namespace App\Domain\Agreements;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementDeleted;
use App\Domain\Agreements\Events\AgreementRestored;
use App\Domain\Agreements\Events\AgreementSigned;
use App\Domain\Agreements\Events\AgreementTrashed;
use App\Domain\Agreements\Events\AgreementUpdated;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\Users\Actions\UpdateUser;
use App\Domain\Users\Models\Customer;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\Member;
use App\Enums\UserTypesEnum;
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
            $agreement->writeable()->save();
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

    public function onAgreementSigned(AgreementSigned $event): void
    {
        $agreement = Agreement::findOrFail($event->aggregateRootUuid());
        $agreement->signed_at = $event->createdAt();
        $agreement->writeable()->save();

        /** Find Current EndUser information */
        $end_user = EndUser::find($event->payload['end_user_id']);

        /** Fetching all agreement with category of end user */
        $agreements = Agreement::with('categoryById')->whereEndUserId($event->payload['end_user_id'])->get();

        /** Checking if any agreement is of membership category */
        $is_membership_agreement = false;
        foreach ($agreements as $agreement) {
            if ($agreement->categoryById && $agreement->categoryById['name'] === AgreementCategory::NAME_MEMBERSHIP) {
                $is_membership_agreement = true;
            }
        }

        $active = $event->payload['active'];

        /** Convert user type to customer/member */
        if ($active) {
            UpdateUser::run(
                $event->payload['end_user_id'],
                [
                    'user_type' => $is_membership_agreement ?
                        UserTypesEnum::MEMBER : UserTypesEnum::CUSTOMER,
                ]
            );
        }
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
