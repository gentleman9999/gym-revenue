<?php

namespace App\Domain\Users\Projectors;

use App\Domain\Users\Events\AccessTokenGranted;
use App\Domain\Users\Events\EndUserClaimedByRep;
use App\Domain\Users\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\Users\Events\EndUserWasCalledByRep;
use App\Domain\Users\Events\EndUserWasEmailedByRep;
use App\Domain\Users\Events\EndUserWasTextMessagedByRep;
use App\Domain\Users\Events\UserReceivedEmail;
use App\Domain\Users\Events\UserReceivedTextMsg;
use App\Domain\Users\Events\UserSetCustomCrudColumns;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\User;
use App\Domain\Users\Services\Helpers\UserDataReflector;
use App\Enums\UserHistoryTypeEnum;
use App\Enums\UserTypesEnum;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserActivityProjector extends Projector
{
    public function onUserReceivedTextMsg(UserReceivedTextMsg $event): void
    {
        $user = User::findOrFail($event->aggregateRootUuid());

        if ($user->user_type == UserTypesEnum::EMPLOYEE) {
            //TODO: Create Projection Table for User Communication History
//            UserDetails::createOrUpdateRecord(
//                $event->aggregateRootUuid(),
//                UserDetailsFieldEnum::SMS_TRANSMISSION->value,
//                $event->template,
//                [
//                    'response' => $event->response,
//                    'client' => $event->client ?? null,
//                ]
//            );
        }
    }

    public function onUserReceivedEmail(UserReceivedEmail $event): void
    {
        $user = User::findOrFail($event->aggregateRootUuid());

        if ($user->user_type == UserTypesEnum::EMPLOYEE) {
            //TODO: Create Projection Table for User Communication History
//            UserDetails::createOrUpdateRecord(
//                $event->aggregateRootUuid(),
//                UserHistoryTypeEnum::EMAIL_TRANSMISSION->value,
//                $event->template,
//                [
//                    'response' => $event->response,
//                    'client' => $event->client ?? null,
//                ]
//            );
        }
    }

    public function onUserSetCustomCrudColumns(UserSetCustomCrudColumns $event): void
    {
        $user = User::findOrFail($event->aggregateRootUuid());

        if ($user->user_type == UserTypesEnum::EMPLOYEE) {
            $user->details[UserHistoryTypeEnum::COLUMN_CONFIG->value] = [$event->table => $event->fields] ;
        }
    }

    public function onAccessTokenGranted(AccessTokenGranted $event): void
    {
        $user = User::findOrFail($event->aggregateRootUuid());

        if ($user->user_type == UserTypesEnum::EMPLOYEE) {
            $user->tokens()->delete();
            $token = $user->createToken($user->email)->plainTextToken;
            $user = User::findOrFail($user->id);
            $user->forceFill(['access_token' => base64_encode($token)]);
            $user->save();
        }
    }

    public function onEndUserClaimedByRep(EndUserClaimedByRep $event): void
    {
        //TODO: Create Projection Table for User Communication History
//        DB::transaction(function () use ($event) {
//            $end_user = Lead::findOrFail($event->aggregateRootUuid());
//            UserDetails::createOrUpdateRecord(
//                $end_user->id,
//                UserHistoryTypeEnum::OWNER_USER_ID->value,
//                $event->claimed_by_user_id
//            );
//        });
    }

    public function onEndUserWasEmailedByRep(EndUserWasEmailedByRep $event): void
    {
        $end_user = User::findOrFail($event->aggregateRootUuid());
        if (! $end_user->isEndUser()) {
            return;
        }
        $user = User::find($event->userId());

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        //TODO: Create Projection Table for User Communication History
//        (new UserDetails())->createOrUpdateRecord(
//            $end_user->id,
//            UserHistoryTypeEnum::EMAILED_BY_REP->value,
//            $event->modifiedBy(),
//            $misc
//        );
    }

    public function onEndUserWasTextMessagedByRep(EndUserWasTextMessagedByRep $event): void
    {
        $end_user = User::findOrFail($event->aggregateRootUuid());
        if (! $end_user->isEndUser()) {
            return;
        }

        $misc = $event->payload;

        //TODO: Create Projection Table for User Communication History
//        (new UserDetails())->createOrUpdateRecord(
//            $end_user->id,
//            UserHistoryTypeEnum::SMS_BY_REP->value,
//            $event->modifiedBy(),
//            $misc
//        );
    }

    public function onEndUserWasCalledByRep(EndUserWasCalledByRep $event): void
    {
        $end_user = User::findOrFail($event->aggregateRootUuid());
        if (! $end_user->isEndUser()) {
            return;
        }
        $user = User::find($event->modifiedBy());

        $misc = $event->payload;
        $misc['user_email'] = $user->email;

        //TODO: Create Projection Table for User Communication History
//        (new UserDetails())->createOrUpdateRecord(
//            $end_user->id,
//            UserHistoryTypeEnum::CALLED_BY_REP->value,
//            $event->modifiedBy(),
//            $misc
//        );

        //TODO: THIS NEEDS TO BE REFACTORED.  FIRE OFF A CreateNote Action in the REACTOR
        if (isset($misc['notes'])) {
            /** @TODO: use action */
            Note::create([
                'entity_id' => $end_user->id,
                'entity_type' => ($end_user::getDetailsModel())::class,
                'note' => $misc['notes'],
                'title' => 'Call Notes',
                'created_by_user_id' => $event->modifiedBy(),
            ]);
        }
    }

    public function onEndUserUpdatedCommunicationPreferences(EndUserUpdatedCommunicationPreferences $event): void
    {
        $end_user = User::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable();
        $end_user->forceFill(['unsubscribed_email' => $event->email, 'unsubscribed_sms' => $event->sms])->save();
        UserDataReflector::reflectData($end_user);
    }
}
