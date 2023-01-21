<?php

declare(strict_types=1);

use App\Domain\Clients\Actions\CreateClient;
use App\Domain\Clients\Projections\Client;
use App\Domain\Notes\Actions\CreateNoteFromContactCall;
use App\Domain\Notes\Actions\CreateNoteFromEndpoint;
use App\Domain\Notes\Actions\DeleteNote;
use App\Domain\Notes\Actions\TrashNote;
use App\Domain\Notes\Actions\UpdateNote;
use App\Domain\Notes\Model\Note;
use App\Domain\Users\Models\Lead;
use App\Enums\UserTypesEnum;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

it('should create note on createNoteFromCall action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);

    $endU = createEndUser([
        'client_id' => $client->id,
        'home_location_id' => $location->gymrevenue_id,
        'user_type' => UserTypesEnum::LEAD,
        'opportunity' => rand(0, 3),
    ]);

    $lead = Lead::where('id', $endU->id)->get();

    $outcome = ['CONTACTED','VOICEMAIL','HUNG_UP','WRONG_NUMBER','APPOINTMENT','SALE'];
    $rand = rand(0, count($outcome) - 1);
    $notes = fake()->paragraph();
    $note = CreateNoteFromContactCall::run([
        'method' => 'phone',
        'outcome' => $outcome[$rand],
        'notes' => $notes,
    ], $user, $lead[0]);

    $this->assertEquals($note->entity_id, $lead[0]->id);
    $this->assertEquals($note->category, $outcome[$rand]);
    $this->assertEquals($note->created_by_user_id, $client->id);
    $this->assertEquals($note->title, $outcome[$rand].'-('.$lead[0]->first_name.' '. $lead[0]->last_name.')');
    $this->assertEquals($note->active, 1);
    $this->assertEquals($note->entity_type, 'App\Domain\Users\Models\Lead');
});

it('should create note on createNoteFromEndpoint action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);

    $endU = createEndUser([
        'client_id' => $client->id,
        'home_location_id' => $location->gymrevenue_id,
        'user_type' => UserTypesEnum::LEAD,
        'opportunity' => rand(0, 3),
    ]);

    $lead = Lead::where('id', $endU->id)->get();

    $active = rand(0, 1);
    $title = fake()->word();
    $notes = fake()->paragraph();
    $note = CreateNoteFromEndpoint::run([
        'title' => $title,
        'active' => $active,
        'note' => $notes,
    ], $user, $lead[0]);

    $this->assertEquals($title, $note->title);
    $this->assertEquals($notes, $note->note);
    $this->assertEquals($active, $note->active);
});

it('should delete Note on deleteNote action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);

    $endU = createEndUser([
        'client_id' => $client->id,
        'home_location_id' => $location->gymrevenue_id,
        'user_type' => UserTypesEnum::LEAD,
        'opportunity' => rand(0, 3),
    ]);

    $lead = Lead::where('id', $endU->id)->get();

    $outcome = ['CONTACTED','VOICEMAIL','HUNG_UP','WRONG_NUMBER','APPOINTMENT','SALE'];
    $rand = rand(0, count($outcome) - 1);
    $notes = fake()->paragraph();
    $note = CreateNoteFromContactCall::run([
        'method' => 'phone',
        'outcome' => $outcome[$rand],
        'notes' => $notes,
    ], $user, $lead[0]);

    $this->assertGreaterThan(0, Note::all()->count());
    DeleteNote::run($note);
    $this->assertLessThan(1, Note::all()->count());
});

it('should updateNote on updateNote action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);

    $endU = createEndUser([
        'client_id' => $client->id,
        'home_location_id' => $location->gymrevenue_id,
        'user_type' => UserTypesEnum::LEAD,
        'opportunity' => rand(0, 3),
    ]);

    $lead = Lead::where('id', $endU->id)->get();

    $outcome = ['CONTACTED','VOICEMAIL','HUNG_UP','WRONG_NUMBER','APPOINTMENT','SALE'];
    $rand = rand(0, count($outcome) - 1);
    $rand2 = rand(0, count($outcome) - 1);
    $notes = fake()->paragraph();
    $notes2 = fake()->paragraph();
    $title = fake()->word();

    $note = CreateNoteFromContactCall::run([
        'method' => 'phone',
        'outcome' => $outcome[$rand],
        'notes' => $notes,
    ], $user, $lead[0]);

    $newNote = UpdateNote::run($note->id, [
        'method' => 'phone',
        'category' => $outcome[$rand2],
        'note' => $notes2,
        'title' => $title,
    ], $user);
    $this->assertEquals($newNote->entity_id, $lead[0]->id);
    $this->assertEquals($newNote->category, $outcome[$rand2]);
    $this->assertEquals($newNote->created_by_user_id, $client->id);
    $this->assertEquals($newNote->title, $title);
    $this->assertEquals($newNote->entity_type, 'App\Domain\Users\Models\Lead');

    $this->assertEquals($note->entity_id, $note->entity_id);

    if ($rand == $rand2) {
        $this->assertEquals($newNote->category, $note->category);
    } else {
        $this->assertNotEquals($newNote->category, $note->category);
    }

    $this->assertEquals($newNote->created_by_user_id, $note->created_by_user_id);
    $this->assertNotEquals($newNote->title, $note->title);
    $this->assertNotEquals($newNote->note, $note->note);
});

it('should trashNote on trashNote action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);

    $endU = createEndUser([
        'client_id' => $client->id,
        'home_location_id' => $location->gymrevenue_id,
        'user_type' => UserTypesEnum::LEAD,
        'opportunity' => rand(0, 3),
    ]);

    $lead = Lead::where('id', $endU->id)->get();

    $outcome = ['CONTACTED','VOICEMAIL','HUNG_UP','WRONG_NUMBER','APPOINTMENT','SALE'];
    $rand = rand(0, count($outcome) - 1);
    $notes = fake()->paragraph();
    $note = CreateNoteFromContactCall::run([
        'method' => 'phone',
        'outcome' => $outcome[$rand],
        'notes' => $notes,
    ], $user, $lead[0]);

    $trashedNote = TrashNote::run($note);
    $this->assertNotEquals($trashedNote->deleted_at, null);
});
