<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\MemberGroups\Actions\CreateMemberGroup;
use App\Domain\MemberGroups\Actions\DeleteMemberGroup;
use App\Domain\MemberGroups\Actions\RestoreMemberGroup;
use App\Domain\MemberGroups\Actions\TrashMemberGroup;
use App\Domain\MemberGroups\Actions\UpdateMemberGroup;
use App\Domain\MemberGroups\Events\MemberGroupCreated;
use App\Domain\MemberGroups\Events\MemberGroupUpdated;
use App\Domain\MemberGroups\Projections\MemberGroup;

beforeEach(function () {
    //
});

function CreateMemberGroup(array $attributes = []): MemberGroup
{
    return CreateMemberGroup::run($attributes + MemberGroup::factory()->raw() + [
        'client_id' => Client::factory()->create()->id,
    ]);
}

it('should update member group name on UpdateMemberGroup action', function () {
    $newName = fake()->unique()->firstName;
    $oldName = fake()->unique()->firstName;

    $member_group = \CreateMemberGroup(['poc_name' => $oldName]);
    $this->assertEquals($member_group->poc_name, $oldName);
    UpdateMemberGroup::run($member_group, ['poc_name' => $newName,]);
    $member_group->refresh();

    $this->assertEquals($member_group->poc_name, $newName);
});

it('should produce a UpdateMemberGroup event', function () {
    $newName = fake()->unique()->firstName;
    $oldName = fake()->unique()->firstName;

    $member_group = \CreateMemberGroup(['poc_name' => $oldName]);
    UpdateMemberGroup::run($member_group, ['poc_name' => $newName,]);
    $member_group->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(MemberGroupUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a MemberGroupCreated event', function () {
    CreateMemberGroup();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(MemberGroupCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a member group on DeleteMemberGroup action', function () {
    $member_group = CreateMemberGroup();

    $this->assertEquals(1, MemberGroup::count());

    DeleteMemberGroup::run($member_group->id);

    $this->assertEquals(0, MemberGroup::count());
});

it('should Trash teams by the TrashMemberGroup action', function () {
    $member_group = CreateMemberGroup();

    $this->assertEquals(null, $member_group->deleted_at);

    TrashMemberGroup::run($member_group->id);
    $member_group->refresh();

    $this->assertNotEquals(null, $member_group->deleted_at);
});

it('should restore member group on RestoreMemberGroup action', function () {
    $member_group = CreateMemberGroup();

    $this->assertEquals(null, $member_group->deleted_at);

    TrashMemberGroup::run($member_group->id);
    $member_group->refresh();

    $this->assertNotEquals(null, $member_group->deleted_at);

    RestoreMemberGroup::run($member_group);

    $this->assertEquals(null, $member_group->deleted_at);
});
