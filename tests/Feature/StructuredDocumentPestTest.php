<?php

declare(strict_types=1);

use App\Domain\StructuredDocuments\Actions\CreateStructuredDocument;
use App\Domain\StructuredDocuments\Actions\DeleteStructuredDocument;
use App\Domain\StructuredDocuments\Actions\RestoreStructuredDocument;
use App\Domain\StructuredDocuments\Actions\TrashStructuredDocument;
use App\Domain\StructuredDocuments\Actions\UpdateStructuredDocument;
use App\Domain\StructuredDocuments\Events\StructuredDocumentCreated;
use App\Domain\StructuredDocuments\Events\StructuredDocumentUpdated;
use App\Domain\StructuredDocuments\Projections\StructuredDocument;

beforeEach(function () {
    //
});

function createStructuredDocument(array $attributes = []): StructuredDocument
{
    return CreateStructuredDocument::run($attributes + StructuredDocument::factory()->raw());
}

it('should produce a StructuredDocumentCreated event', function () {
    createStructuredDocument();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(StructuredDocumentCreated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a StructuredDocumentUpdated event', function () {
    $structuredDocument = createStructuredDocument();
    UpdateStructuredDocument::run($structuredDocument, ['ttl' => now()]);
    $structuredDocument->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(StructuredDocumentUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a structured document on DeleteStructuredDocument action', function () {
    $structuredDocument = createStructuredDocument();

    $this->assertEquals(1, StructuredDocument::count());

    DeleteStructuredDocument::run($structuredDocument->id);

    $this->assertEquals(0, StructuredDocument::count());
});

it('should Trash a structured document on TrashStructuredDocument action', function () {
    $structuredDocument = createStructuredDocument();

    $this->assertEquals(null, $structuredDocument->deleted_at);

    TrashStructuredDocument::run($structuredDocument->id);
    $structuredDocument->refresh();

    $this->assertNotEquals(null, $structuredDocument->deleted_at);
});

it('should restore structured document on RestoreStructuredDocument action', function () {
    $structuredDocument = createStructuredDocument();

    $this->assertEquals(null, $structuredDocument->deleted_at);

    TrashStructuredDocument::run($structuredDocument->id);
    $structuredDocument->refresh();

    $this->assertNotEquals(null, $structuredDocument->deleted_at);

    RestoreStructuredDocument::run($structuredDocument);

    $this->assertEquals(null, $structuredDocument->deleted_at);
});
