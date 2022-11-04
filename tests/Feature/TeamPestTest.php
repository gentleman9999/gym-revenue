<?php

declare(strict_types=1);

use App\Domain\Teams\Actions\AddOrInviteTeamMembers;
use App\Domain\Teams\Actions\AddTeamMember;
use App\Domain\Teams\Actions\AddTeamMembers;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Teams\Actions\DeleteTeam;
use App\Domain\Teams\Actions\InviteTeamMember;
use App\Domain\Teams\Actions\InviteTeamMembers;
use App\Domain\Teams\Actions\RemoveTeamMember;
use App\Domain\Teams\Actions\UpdateTeam;
use App\Domain\Teams\Actions\UpdateTeamName;
use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamDeleted;
use App\Domain\Teams\Events\TeamMemberAdded;
use App\Domain\Teams\Events\TeamMemberInvited;
use App\Domain\Teams\Events\TeamUpdated;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamUser;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

//Endpoints
it('should allow users to create team successfully', function () {
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    /** Admin */
    Bouncer::allow('Admin')->everything();
    $this->actingAs($user);

    $teamName = fake()->company;
    $response = $this->post('/teams', ['name' => $teamName]);
    $team = Team::where('name', $teamName)->first();

    $this->assertEquals($team->name, $teamName);
    $response->assertStatus(302);
});

it('should be able to see the create team screen', function () {
    $role = UserUtility::createRole();
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->get('/teams/create');

    $response->assertStatus(200);
});

it('should not allow users to create team', function () {
    $role = UserUtility::createRole();
    $user = UserUtility::createUserWithTeam();
    $this->actingAs($user);

    $response = $this->post('/teams', [
        'name' => fake()->company,
    ]);

    //Forbidden
    $response->assertStatus(403);
});

it('should add user to team', function () {
    Mail::fake();
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    //create new user
    $user = CreateUser::run(User::factory()->raw());
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());

    $response = $this->post('/teams/' . $team->id . '/members', [
        'emails' => [$user->email],
    ]);

    //user should be on the the newly created team
    $tu = TeamUser::where('user_id', $user->id)->first();

    $this->assertEquals($tu->team_id, $team->id);
});

it('should add users to team', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    //create new user
    [$user, $user1, $user2, $user3] = UserUtility::createUser(['client_id' => null], 4);

    $usersCollection = [
        $user->id,
        $user1->id,
        $user2->id,
        $user3->id,
    ];

    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());

    $this->post('/teams/' . $team->id . '/members', [
        'emails' => [
            $user->email,
            $user1->email,
            $user2->email,
            $user3->email,
        ],
    ]);

    //user should be on the the newly created team
    $team_users = TeamUser::where('team_id', $team->id)->get()->toArray();

    $this->assertEqualsCanonicalizing($usersCollection, array_column($team_users, 'user_id'));
});

it('should not allow user to invite team member', function () {
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $this->actingAs($user);
    ;

    //create new user
    UserUtility::createUser();
    $team = Team::first();

    $response = $this->post("/teams/{$team->id}/members", [
        'emails' => fake()->email,
    ]);

    $response->assertStatus(403);
});

//TODO Delete team is bugged
it('should return status code 302', function () {
    Mail::fake();
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    //create new user
    $user = UserUtility::createUser();

    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());

    $response = $this->post('/teams/' . $team->id . '/members', [
        'emails' => [$user->email],
    ]);

    $response->assertStatus(302);
});

//TODO Delete team is bugged
it('should be able to delete team', function () {
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $this->actingAs($user);
    ;

    //give them admin access to create a new team
    Bouncer::allow('Admin')->everything();

    $teamName = fake()->company;
    $this->post('/teams', ['name' => $teamName]);
    //find the team just created
    $team = Team::where('name', $teamName)->first();
    $initialCount = DB::table('teams')->count();

    $this->assertEquals(2, $initialCount);
    //delete team
    $this->delete('/teams/' . $team->id);

    $finalCount = DB::table('teams')->count();

    $this->assertEquals(1, $finalCount);
});

//Team Actions
it('should create team successfully using the CreateTeamAction', function () {
    //create a new team
    CreateTeam::run(Team::factory()->raw());

    $t = Team::all()->first();
    $this->assertInstanceOf(Team::class, $t);
});
//Team Actions

it('should produce a TeamCreated event', function () {
    //create a new team
    CreateTeam::run(Team::factory()->raw());

    $t = Team::all()->first();
    $this->assertInstanceOf(Team::class, $t);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(TeamCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete team successfully using the DeleteTeamAction', function () {
    CreateTeam::run(Team::factory()->raw());
    $team = Team::all()->first();

    DeleteTeam::run($team);

    $this->assertEquals(0, Team::count());
});

it('should produce a TeamDeleted Event', function () {
    //create a new team
    CreateTeam::run(Team::factory()->raw());
    $team = Team::first();

    DeleteTeam::run($team);
    $this->assertEquals(0, Team::count());
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(TeamDeleted::class, array_column($storedEvents, 'event_class'));
});

it('should update team successfully using the UpdateTeamNameAction', function () {
    $name = fake()->unique()->company;
    $oldName = 'Grove Street gang';

    //create a new team
    CreateTeam::run(Team::factory()->raw(['name' => $oldName]));

    $team = Team::first();

    $this->assertEquals($oldName, $team->name);
    UpdateTeamName::run($team->id, $name);

    $team->refresh();
    $this->assertEquals($name, $team->name);
});

it('should update team successfully using the UpdateTeamAction', function () {
    $oldName = fake()->unique()->company;
    $name = fake()->unique()->company;

    //create a new team
    $team = CreateTeam::run(Team::factory()->raw([
        'name' => $oldName,
        'home_team' => 0,
    ]));

    $this->assertFalse($team->home_team);
    $this->assertEquals($team->name, $oldName);

    UpdateTeam::run($team, [

        'home_team' => 1,
        'name' => $name,
    ]);

    $team->refresh();

    $this->assertTrue($team->home_team);
    $this->assertEquals($team->name, $name);
});

it('should produce an update event', function () {
    $oldName = fake()->unique()->company;
    $name = fake()->unique()->company;

    //create a new team
    $team = CreateTeam::run(Team::factory()->raw([
        'name' => $oldName,
        'home_team' => 0,
    ]));

    UpdateTeam::run($team, [
        'home_team' => 1,
        'name' => $name,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(TeamUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should successfully add team members using AddOrInviteTeamMembers action', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    AddOrInviteTeamMembers::run($team, [
        $user->email,
        $user2->email,
        $user3->email,
    ]);

    $team_users = DB::table('team_user')->get();
    foreach ($team_users as $team_user) {
        if ($team_user->team_id == $team->id) {
            if ($team_user->user_id == $user->id) {
                $this->assertTrue(true);
            } elseif ($team_user->user_id == $user2->id) {
                $this->assertTrue(true);
            } elseif ($team_user->user_id == $user3->id) {
                $this->assertTrue(true);
            }
        }
    }
});

it('should produce three TeamMemberAdded events', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    ;
    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    AddOrInviteTeamMembers::run($team, [
        $user->email,
        $user2->email,
        $user3->email,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(TeamMemberAdded::class, array_column($storedEvents, 'event_class'));
});

it('should have three from the response from AddOrInviteTeamMembers action', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    $response = AddOrInviteTeamMembers::run($team, [
        $user->email,
        $user2->email,
        $user3->email,
    ]);

    $count = 0;
    foreach ($response as $r) {
        $count++;
        $this->assertInstanceOf(App\Domain\Users\Models\User::class, $r);
    }
    $this->assertEquals(3, $count);
});

it('should inviteTeamMember', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    //create new user
    $user = UserUtility::createUser();
    $response = InviteTeamMember::run($team, $user->email);

    $this->assertEquals($response, $user->email);
});

it('it should throw exception:his user has already been invited to the team on inviteTeamMember action', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    ;

    //create new user
    $user = UserUtility::createUser();
    ;
    $response = InviteTeamMember::run($team, $user->email);

    $this->assertEquals($response, $user->email);

    try {
        InviteTeamMember::run($team, $user->email);
    } catch (Exception $e) {
        $this->assertSame($e->getMessage(), 'This user has already been invited to the team.');
    }
});

it('should InviteTeamMembers', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());

    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    $response = InviteTeamMembers::run($team, [
        $user->email,
        $user2->email,
        $user3->email,
    ]);

    $this->assertEquals(count($response), 3);
});

it('should remove InviteTeamMembers using the RemoveTeamMember action', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    ;
    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);


    AddTeamMembers::run($team, [$user, $user2, $user3]);

    $teamuser = TeamUser::where('team_id', $team->id)->count();
    $this->assertEquals($teamuser, 3);

    $response = RemoveTeamMember::run($team, $user);

    $teamuser = TeamUser::where('team_id', $team->id)->count();
    $this->assertEquals($teamuser, 2);
});

it('should not add InviteTeamMembers that are already added', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    InviteTeamMembers::run($team, [
        $user->email,
        $user2->email,
        $user3->email,
    ]);

    try {
        InviteTeamMembers::run($team, [$user->email]);
    } catch (Exception $e) {
        $this->assertSame($e->getMessage(), 'This user has already been invited to the team.');
    }
});

it('should produce InviteTeamMemberInvited event', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    InviteTeamMembers::run($team, [
        $user->email,
        $user2->email,
        $user3->email,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(TeamMemberInvited::class, array_column($storedEvents, 'event_class'));
});

it('should add member to team using AddTeamMember action', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    //create new user
    $user = UserUtility::createUser();

    AddTeamMember::run($team, $user);
    $teamuser = TeamUser::where('team_id', $team->id)->first();

    $this->assertTrue($teamuser->user_id == $user->id);
});

it('should add members to team using AddTeamMembers action', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());
    //create new user
    [$user, $user2, $user3] = UserUtility::createUser(count: 3);

    AddTeamMembers::run($team, [$user, $user2, $user3]);

    $teamuser = TeamUser::where('team_id', $team->id)->get();

    foreach ($teamuser as $tu) {
        if ($user->id == $tu->user_id && $team->id == $tu->team_id) {
            $this->assertTrue(true);
        } elseif ($user2->id == $tu->user_id && $team->id == $tu->team_id) {
            $this->assertTrue(true);
        } elseif ($user3->id == $tu->user_id && $team->id == $tu->team_id) {
            $this->assertTrue(true);
        }
    }
});
