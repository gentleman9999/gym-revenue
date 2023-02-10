<?php

declare(strict_types=1);

use App\Domain\Agreements\Actions\CreateAgreement;
use App\Domain\Agreements\Actions\SignAgreement;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\AgreementTemplates\Actions\CreateAgreementTemplate;
use App\Domain\BillingSchedules\Actions\CreateBillingSchedule;
use App\Domain\CalendarAttendees\Actions\AddCalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarEvents\Actions\CreateCalendarEvent;
use App\Domain\CalendarEventTypes;
use App\Domain\CalendarEventTypes\Actions\CreateCalendarEventType;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Clients\Actions\CreateClient;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Reminders\Actions\CreateReminderFromCalendarEvent;
use App\Domain\Reminders\Actions\DeleteReminder;
use App\Domain\Reminders\Actions\UpdateReminder;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Enums\AgreementAvailabilityEnum;
use App\Enums\BillingScheduleTypesEnum;
use App\Enums\UserTypesEnum;
use App\Support\Uuid;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});
//function createLocation(array $attributes = []): Location
//{
//    return CreateLocation::run($attributes + Location::factory()->raw() + [
//            'shouldCreateTeam' => true,
//            'client_id' => Client::factory()->create()->id,
//        ]);
//}
function randomBirthday(int $age)
{
    $min = strtotime("jan 1st -".$age." years");
    $max = strtotime("dec 31st -18 years");
    $time = rand($min, $max);

    return date("Y-m-d H:i:s", $time);
}

function signedAgreement($data)
{
    $sign_agreement_data['user_id'] = $data['end_user_id'];
    $sign_agreement_data['active'] = true;
    SignAgreement::run($sign_agreement_data, $data['id']);
}

function agreementTemplate(Client $client, Location $location)
{
    $client_id = $client->id;
    $billingSchedule = billingSchedule($client_id);

    $agreement_data['client_id'] = $client_id;
    $agreement_data['gr_location_id'] = $location->gymrevenue_id;
    $agreement_data['agreement_name'] = 'test contract for '.$client->name;
    $agreement_data['agreement_json'] = json_encode($client);
    $agreement_data['availability'] = randomAgreementAvailability();
    $agreement_data['billing_schedule_id'] = $billingSchedule->id;
    $agreement_data['contract_id'] = Uuid::new();

    return CreateAgreementTemplate::run($agreement_data);
}
function randomAgreementAvailability()
{
    $agreement_availability_random = AgreementAvailabilityEnum::asArray();

    return $agreement_availability_random[array_rand($agreement_availability_random)];
}

function billingSchedule($client_id)
{
    $billing_schedule['client_id'] = $client_id;
    $billing_schedule['type'] = BillingScheduleTypesEnum::PAID_IN_FULL;
    $billing_schedule['is_open_ended'] = false;
    $billing_schedule['is_renewable'] = false;
    $billing_schedule['should_renew_automatically'] = false;
    $billing_schedule['term_length'] = 0;
    $billing_schedule['min_terms'] = 0;
    $billing_schedule['amount'] = rand(1, 99) . "." .rand(0, 99);

    return CreateBillingSchedule::run($billing_schedule);
}

function makeAgreement(array $data)
{
    $agreement_data['client_id'] = $data['client_id'];
    $agreement_data['agreement_category_id'] = $data['agreement_category_id'];
    $agreement_data['gr_location_id'] = $data['gr_location_id'];
    $agreement_data['created_by'] = $data['created_by'];
    $agreement_data['user_id'] = $data['end_user_id'];
    $agreement_data['agreement_template_id'] = $data['agreement_template_id'];
    $agreement_data['agreement_json'] = json_encode([]);
    $agreement_data['active'] = true;

    return CreateAgreement::run($agreement_data);
}

function createEndUser(array $userData): User
{
    $factoryData = User::factory()->raw($userData);
    $factoryData['email_verified_at'] = $factoryData['email_verified_at']->format('Y-m-d h:m:s');
    $lead = CreateUser::run($factoryData);

    return $lead;
}

it('should do get to tasks', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->get('/tasks');

    $response->assertStatus(302);
});

it('should create task', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
//    Bouncer::allow($role->name)->everything();
//    $this->actingAs($user);
    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];
    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventTypes\CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => [],
        "lead_attendees" => [],
        "member_attendees" => [],
        "location_id" => $location->id,
        "client_id" => $client->id,
    ];

    $calendarEvent = CreateCalendarEvent::run($data, $user);
    $this->assertEquals($calendarEvent->description, $description);
    $this->assertEquals($calendarEvent->full_day_event, 0);
    $this->assertEquals($calendarEvent->start, $todaysDate);
    $this->assertEquals($calendarEvent->end, $todaysDate);
    $this->assertEquals($calendarEvent->client_id, $client->id);
    $this->assertEquals($calendarEvent->event_type_id, $taskEventId);
});

it('should do get to calendar', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->get('/calendar');

    $response->assertStatus(302);
});

it('should create task with leads, members and users', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
//    Bouncer::allow($role->name)->everything();
//    $this->actingAs($user);
    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];

    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $clientId = $taskEvent->client_id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $clientId]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $end_users = [];
    $users = [];
    $members = [];


    for ($c = 0;$c < rand(1, 15);$c++) {
        $endU = createEndUser([
            'client_id' => $client->id,
            'home_location_id' => $location->gymrevenue_id,
            'user_type' => UserTypesEnum::LEAD,
            'opportunity' => rand(0, 3),
        ]);

        $end_users[] = $endU->id;
    }

    for ($c = 0;$c < rand(1, 15);$c++) {
        $u = UserUtility::createUserWithTeam();
        $users[] = $u->id;
    }

    $agreementTemplate = agreementTemplate($client, $location);
    $agreementCategory = AgreementCategory::where('client_id', $client->id)->first()->toArray();

    for ($c = 0;$c < sizeof($end_users);$c++) {
        if ($c % 2 == 0) {
            $members[] = $end_users[$c];
            makeAgreement([
                'client_id' => $client->id,
                'agreement_category_id' => $agreementCategory['id'],
                'gr_location_id' => $location->gymrevenue_id,
                'created_by' => $user->id,
                'end_user_id' => $end_users[$c],
                'agreement_template_id' => $agreementTemplate->id,
            ]);
        }
    }

    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => $users,
        "lead_attendees" => $end_users,
        "member_attendees" => $members,
        "location_id" => $location->id,
        "client_id" => $clientId,
    ];

    //The number of attendees has to be the number of leads plus one because that includes
    //the user attending

    $calendarEvent = CreateCalendarEvent::run($data, $user);
    $calendarAttendee = CalendarAttendee::where('calendar_event_id', $calendarEvent->id)->get();
    $calendarAttendeeArray = $calendarAttendee->toArray();

    $specificAttendees = true;

    $this->assertEquals(count($end_users) + 1 + count($users), count($calendarAttendeeArray));
    for ($c = 0;$c < sizeof($calendarAttendeeArray);$c++) {
        if (! (in_array($calendarAttendeeArray[$c]['entity_data']['id'], $end_users))) {
            if (! $user->id == $calendarAttendeeArray[$c]['entity_data']['id']) {
                $specificAttendees = false;
            }
        }
    }
    $this->assertTrue($specificAttendees);
});

it('should create reminder', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
//    Bouncer::allow($role->name)->everything();
//    $this->actingAs($user);
    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];
    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventTypes\CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => [],
        "lead_attendees" => [],
        "member_attendees" => [],
        "location_id" => $location->id,
        "client_id" => $client->id,
    ];

    $calendarEvent = CreateCalendarEvent::run($data, $user);

    $reminder = CreateReminderFromCalendarEvent::run(['entity_id' => $calendarEvent->id,'user_id' => $user->id]);

    $this->assertEquals($reminder->entity_id, $calendarEvent->id);
    $this->assertEquals($reminder->entity_type, "App\Domain\CalendarEvents\CalendarEvent");
});

it('should delete reminder', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
//    Bouncer::allow($role->name)->everything();
//    $this->actingAs($user);
    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];
    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventTypes\CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => [],
        "lead_attendees" => [],
        "member_attendees" => [],
        "location_id" => $location->id,
        "client_id" => $client->id,
    ];

    $calendarEvent = CreateCalendarEvent::run($data, $user);

    $reminder = CreateReminderFromCalendarEvent::run(['entity_id' => $calendarEvent->id,'user_id' => $user->id]);

    DeleteReminder::run($reminder);

    $this->assertEquals(Reminder::find($reminder->id), null);
});

it('should update reminder', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
//    Bouncer::allow($role->name)->everything();
//    $this->actingAs($user);
    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];
    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventTypes\CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $client->id]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => [],
        "lead_attendees" => [],
        "member_attendees" => [],
        "location_id" => $location->id,
        "client_id" => $client->id,
    ];

    $calendarEvent = CreateCalendarEvent::run($data, $user);

    $reminder = CreateReminderFromCalendarEvent::run(['entity_id' => $calendarEvent->id,'user_id' => $user->id]);

    $originalData['remind_time'] = $reminder->remind_time;
    $originalData['description'] = $reminder->description;
    $originalData['name'] = $reminder->name;
    $originalData['id'] = $reminder->id;

    UpdateReminder::run($reminder, [
        'description' => fake()->paragraph(),
        'name' => fake()->word(),
        'remind_time' => rand(31, 120),
    ]);

    $reminder->refresh();

    $this->assertEquals($originalData['id'], $reminder->id);
    $this->assertNotEquals($originalData['description'], $reminder->description);
    $this->assertNotEquals($originalData['name'], $reminder->name);
    $this->assertNotEquals($originalData['remind_time'], $reminder->remind_time);
});

it('should create task with members only', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];

    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $clientId = $taskEvent->client_id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $clientId]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $end_users = [];
    $users = [];

    for ($c = 0;$c < rand(1, 15);$c++) {
        $endU = createEndUser([
            'client_id' => $client->id,
            'home_location_id' => $location->gymrevenue_id,
            'user_type' => UserTypesEnum::LEAD,
            'opportunity' => rand(0, 3),
        ]);
        $end_users[] = $endU->id;
    }

    $agreementTemplate = agreementTemplate($client, $location);
    $agreementCategory = AgreementCategory::where('client_id', $client->id)->first()->toArray();

    for ($c = 0;$c < sizeof($end_users);$c++) {
        $agreement = makeAgreement([
            'client_id' => $client->id,
            'agreement_category_id' => $agreementCategory['id'],
            'gr_location_id' => $location->gymrevenue_id,
            'created_by' => $user->id,
            'end_user_id' => $end_users[$c],
            'agreement_template_id' => $agreementTemplate->id,
        ]);
        signedAgreement([
            'id' => $agreement->id,
            'end_user_id' => $end_users[$c],
        ]);
    }
    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => [],
        "lead_attendees" => [],
        "member_attendees" => $end_users,
        "location_id" => $location->id,
        "client_id" => $clientId,
    ];


    $calendarEvent = CreateCalendarEvent::run($data, $user);
    foreach ($end_users as $member_id) {
        $test = \App\Domain\Users\Models\Lead::find($member_id);
        $member = User::find($member_id);
        if ($member) {
            AddCalendarAttendee::run([
                'client_id' => $data['client_id'],
                'entity_type' => Member::class,
                'entity_id' => $member->id,
                'entity_data' => $member,
                'calendar_event_id' => $calendarEvent->id,
                'invitation_status' => 'Invitation Pending',
            ]);
        }
    }
    $calendarAttendee = CalendarAttendee::where('calendar_event_id', $calendarEvent->id)->get();
    $calendarAttendeeArray = $calendarAttendee->toArray();

    $specificAttendees = true;
    // The following fails because the CreateCalendarEvent function doesn't find the Member ID's
    $this->assertEquals(count($end_users) + 1 + count($users), count($calendarAttendeeArray));
    for ($c = 0;$c < sizeof($calendarAttendeeArray);$c++) {
        if (! (in_array($calendarAttendeeArray[$c]['entity_data']['id'], $end_users))) {
            if (! $user->id == $calendarAttendeeArray[$c]['entity_data']['id']) {
                $specificAttendees = false;
            }
        }
    }
    $this->assertTrue($specificAttendees);
});

it('should create task with leads only', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    $payload = [
        'client_id' => $client->id,
        'name' => 'Task',
        'description' => 'Task Event Type for '.$client->name,
        'type' => 'Task',
        'color' => 'green',
    ];

    CreateCalendarEventType::run($payload);
    $taskEvent = CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $clientId = $taskEvent->client_id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $clientId]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $end_users = [];
    $users = [];

    for ($c = 0;$c < rand(1, 15);$c++) {
        $endU = createEndUser([
            'client_id' => $client->id,
            'home_location_id' => $location->gymrevenue_id,
            'user_type' => UserTypesEnum::LEAD,
            'opportunity' => rand(0, 3),
        ]);

        $end_users[] = $endU->id;
    }

    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => $users,
        "lead_attendees" => $end_users,
        "member_attendees" => [],
        "location_id" => $location->id,
        "client_id" => $clientId,
    ];

    $calendarEvent = CreateCalendarEvent::run($data, $user);

    $calendarAttendee = CalendarAttendee::where('calendar_event_id', $calendarEvent->id)->get();
    $calendarAttendeeArray = $calendarAttendee->toArray();

    $specificAttendees = true;

    $this->assertEquals(count($end_users) + 1 + count($users), count($calendarAttendeeArray));
    for ($c = 0;$c < sizeof($calendarAttendeeArray);$c++) {
        if (! (in_array($calendarAttendeeArray[$c]['entity_data']['id'], $end_users))) {
            if (! $user->id == $calendarAttendeeArray[$c]['entity_data']['id']) {
                $specificAttendees = false;
            }
        }
    }
    $this->assertTrue($specificAttendees);
});

it('should create task with users only', function () {
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    $client = CreateClient::run(
        Client::factory()->raw()
    );

    $user->client_id = $client->id;

    CreateCalendarEventType::run([
            'client_id' => $client->id,
            'name' => 'Task',
            'description' => 'Task Event Type for '.$client->name,
            'type' => 'Task',
            'color' => 'green',
    ]);

    $taskEvent = CalendarEventType::where('name', 'Task')->first();
    $taskEventId = $taskEvent->id;
    $clientId = $taskEvent->client_id;
    $todaysDate = date("Y-m-d H:i:s");
    $location = createLocation(['address1' => fake()->unique()->streetName,'client_id' => $clientId]);
    $title = fake()->word();
    $description = fake()->paragraph();
    $end_users = [];
    $users = [];

    for ($c = 0;$c < rand(1, 15);$c++) {
        $u = UserUtility::createUserWithTeam();
        $users[] = $u->id;
    }

    $data = [
        "title" => $title,
        "description" => $description,
        "full_day_event" => false,
        "start" => $todaysDate,
        "end" => $todaysDate,
        "event_type_id" => $taskEventId,
        "user_attendees" => $users,
        "lead_attendees" => $end_users,
        "member_attendees" => [],
        "location_id" => $location->id,
        "client_id" => $clientId,
    ];

    $calendarEvent = CreateCalendarEvent::run($data, $user);

    $calendarAttendee = CalendarAttendee::where('calendar_event_id', $calendarEvent->id)->get();
    $calendarAttendeeArray = $calendarAttendee->toArray();

    $specificAttendees = true;

    $this->assertEquals(count($end_users) + 1 + count($users), count($calendarAttendeeArray));
    for ($c = 0;$c < sizeof($calendarAttendeeArray);$c++) {
        if (! (in_array($calendarAttendeeArray[$c]['entity_data']['id'], $users))) {
            if (! $user->id == $calendarAttendeeArray[$c]['entity_data']['id']) {
                $specificAttendees = false;
            }
        }
    }
    $this->assertTrue($specificAttendees);
});
