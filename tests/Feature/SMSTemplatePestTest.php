<?php

declare(strict_types=1);


use App\Domain\Templates\SmsTemplates\Actions\CreateSmsTemplate;
use App\Domain\Templates\SmsTemplates\Actions\DeleteSmsTemplate;
use App\Domain\Templates\SmsTemplates\Actions\DuplicateSmsTemplate;
use App\Domain\Templates\SmsTemplates\Actions\RestoreSmsTemplate;
use App\Domain\Templates\SmsTemplates\Actions\TrashSmsTemplate;
use App\Domain\Templates\SmsTemplates\Actions\UpdateSmsTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;

beforeEach(function () {
    CreateUser::run(User::factory()->raw([
        'first_name' => 'Alexander',
        'last_name' => 'Isak',
        'email' => 'alex.isak@gmail.com',
        'phone' => '4199081945',
        'zip' => '15859',
    ]));
});

function createSmsTemplate()
{
    $default_markup = "Hello, %name%! Welcome to GymRevenue!  -GymmieBot";

    return CreateSmsTemplate::run([
        'name' => "Baby's First SMS Template (;",
        'active' => 1,
        'created_by_user_id' => 'auto',
        'markup' => $default_markup,
    ]);
}

it('should create SMS Template on CreateSMSTemplate action', function () {
    $sms_template = createSmsTemplate();
    $this->assertTrue($sms_template instanceof SmsTemplate);
});

it('should update SMS Template on UpdateSMSTemplate action', function () {
    $sms_template = createSmsTemplate();
    $data['name'] = 'This is to test updates';
    $data['markup'] = 'Hello!, this is a test!';
    $data['created_by_user_id'] = User::whereEmail('alex.isak@gmail.com')->first()->id;
    $data['user'] = $data['created_by_user_id'];
    $this->assertEquals($sms_template->name, "Baby's First SMS Template (;");
    $this->assertEquals($sms_template->markup, "Hello, %name%! Welcome to GymRevenue!  -GymmieBot");
    $updated_template = UpdateSmsTemplate::run($sms_template, $data);

    $this->assertEquals($updated_template->name, $data['name']);
    $this->assertEquals($updated_template->markup, $data['markup']);
});

it('should delete SMS Template on DeleteSmsTemplate action', function () {
    $sms_template = createSmsTemplate();

    DeleteSmsTemplate::run($sms_template);

    $this->assertEquals(0, SmsTemplate::count());
});

it('should duplicate SMS Template on DuplicateSmsTemplate action', function () {
    $sms_template = createSmsTemplate();

    DuplicateSmsTemplate::run($sms_template->toArray());

    $this->assertEquals(2, SmsTemplate::count());
});

it('should trash SMS Template on TrashSmsTemplate action', function () {
    $sms_template = createSmsTemplate();

    TrashSmsTemplate::run($sms_template);

    $this->assertEquals(1, SmsTemplate::withTrashed()->count());
});

it('should restore SMS Template on RestoreSmsTemplate action', function () {
    $sms_template = createSmsTemplate();
    $trashed_sms_template = TrashSmsTemplate::run($sms_template);

    RestoreSmsTemplate::run($trashed_sms_template);

    $this->assertEquals(1, SmsTemplate::count());
});
