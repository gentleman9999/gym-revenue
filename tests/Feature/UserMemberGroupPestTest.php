<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\MemberGroups\Actions\CreateMemberGroup;
use App\Domain\MemberGroups\Projections\MemberGroup;
use App\Domain\UserMemberGroups\Actions\CreateUserMemberGroup;
use App\Domain\UserMemberGroups\Actions\DeleteUserMemberGroup;
use App\Domain\UserMemberGroups\Actions\UpdateUserMemberGroup;
use App\Domain\UserMemberGroups\Events\UserMemberGroupCreated;
use App\Domain\UserMemberGroups\Events\UserMemberGroupUpdated;
use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

function CreateUserMemberGroup(array $attributes = []): UserMemberGroup
{
    $client_id = Client::factory()->create()->id;
    $member_group_id = CreateMemberGroup::run(MemberGroup::factory()->raw(['client_id' => $client_id]))->id;
    UserUtility::createRole(['name' => 'Admin']);
    $user_id = UserUtility::createUserWithTeam()->id;

    return CreateUserMemberGroup::run($attributes + UserMemberGroup::factory()->raw() + [
        'client_id' => $client_id,
        'member_group_id' => $member_group_id,
        'user_id' => $user_id,
        'is_primary' => 0
    ]);
}

it('should produce a UserMemberGroupCreated event', function () {
    CreateUserMemberGroup();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserMemberGroupCreated::class, array_column($storedEvents, 'event_class'));
});


it('should update UserMemberGroup name on UpdateUserMemberGroup action', function () {
    $new_is_primary = 1;
    $old_is_primary = 0;

    $user_member_group = CreateUserMemberGroup();
    
    $this->assertEquals($user_member_group->is_primary, $old_is_primary);
    UpdateUserMemberGroup::run($user_member_group, ['is_primary' => $new_is_primary]);
    $user_member_group->refresh();

    $this->assertEquals($user_member_group->is_primary, $new_is_primary);
});

it('should produce a UpdateUserMemberGroup event', function () {
    $new_is_primary = 1;
    $old_is_primary = 0;

    $user_member_group = CreateUserMemberGroup();
    UpdateUserMemberGroup::run($user_member_group, ['is_primary' => $new_is_primary]);
    $user_member_group->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserMemberGroupUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a UserMemberGroup on DeleteUserMemberGroup action', function () {
    $user_member_group = CreateUserMemberGroup();

    $this->assertEquals(1, UserMemberGroup::count());

    DeleteUserMemberGroup::run($user_member_group->id);

    $this->assertEquals(0, UserMemberGroup::count());
});
