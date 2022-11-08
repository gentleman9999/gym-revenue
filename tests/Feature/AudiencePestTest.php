<?php

declare(strict_types=1);

use App\Domain\Audiences\Actions\DeleteAudience;
use App\Domain\Audiences\Actions\RestoreAudience;
use App\Domain\Audiences\Actions\TrashAudience;
use App\Domain\Audiences\Actions\UpdateAudience;
use App\Domain\Audiences\Audience;
use App\Domain\Audiences\Events\AudienceCreated;
use App\Domain\Audiences\Events\AudienceRestored;
use App\Domain\Audiences\Events\AudienceTrashed;
use App\Domain\Audiences\Events\AudienceUpdated;
use Tests\Feature\Utilities\AudienceUtility;

beforeEach(function () {
    //
});

it('create audience using the CreateAudience action', function () {
    $audience = AudienceUtility::create();

    $this->assertTrue($audience instanceof Audience);
});

it('should produce an AudienceCreated event', function () {
    AudienceUtility::create();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(AudienceCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete audience with DeleteAudience action', function () {
    $audience = AudienceUtility::create();

    $numberAtCreation = Audience::all()->count();
    DeleteAudience::run($audience->id);
    $numberAtDeletion = Audience::all()->count();

    //should be one less item after deletion
    $this->assertEquals($numberAtCreation - 1, $numberAtDeletion);
});

it('should delete event', function () {
    $audience = AudienceUtility::create();
    DeleteAudience::run($audience->id);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(AudienceCreated::class, array_column($storedEvents, 'event_class'));
});

it('should Trash the an Audience item using the TrashAudience action', function () {
    $audience = AudienceUtility::create();
    TrashAudience::run($audience);
    $audience = Audience::withTrashed()->findOrFail($audience->id);

    $this->assertTrue($audience instanceof Audience);
});

it('should produce a Trash event', function () {
    $audience = AudienceUtility::create();
    TrashAudience::run($audience);
    Audience::withTrashed()->findOrFail($audience->id);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(AudienceTrashed::class, array_column($storedEvents, 'event_class'));
});

it('should restore audience item with action RestoreAudience', function () {
    $audience = AudienceUtility::create();
    TrashAudience::run($audience);

    $audience = Audience::withTrashed()->findOrFail($audience->id);
    $item = Audience::onlyTrashed()->where('id', $audience->id)->get();

    $this->assertFalse($item->isEmpty());
    RestoreAudience::run($audience);

    $item = Audience::onlyTrashed()->where('id', $audience->id)->get();
    $this->assertTrue($item->isEmpty());
});

it('should produce a restore event', function () {
    $audience = AudienceUtility::create();

    TrashAudience::run($audience);
    RestoreAudience::run($audience);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(AudienceRestored::class, array_column($storedEvents, 'event_class'));
});

it('should update an audience item', function () {
    $oldName = fake()->unique()->name;
    $newName = fake()->unique()->name;

    $audience = AudienceUtility::create(['name' => $oldName]);
    UpdateAudience::run($audience, ['name' => $newName,]);

    $this->assertEquals($newName, $audience->name);
});

it('should produce an update event', function () {
    $oldName = fake()->unique()->name;
    $newName = fake()->unique()->name;
    $audience = AudienceUtility::create(['name' => $oldName]);

    UpdateAudience::run($audience, ['name' => $newName]);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(AudienceUpdated::class, array_column($storedEvents, 'event_class'));
});
