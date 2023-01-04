<?php

declare(strict_types=1);

use App\Domain\StructuredDocumentRequests\Actions\CreateStructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\Actions\DeleteStructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\Actions\RestoreStructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\Actions\TrashStructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\Actions\UpdateStructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestCreated;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestUpdated;
use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\Enums\StructuredDocumentEntityTypeEnum;

beforeEach(function () {
    //
});

function createStructuredDocumentRequest(array $attributes = []): StructuredDocumentRequest
{
    return CreateStructuredDocumentRequest::run($attributes + StructuredDocumentRequest::factory()->raw());
}

it('should produce a StructuredDocumentRequestCreated event', function () {
    createStructuredDocumentRequest();
    $stored_events = DB::table('stored_events')->get()->toArray();

    $this->assertContains(StructuredDocumentRequestCreated::class, array_column($stored_events, 'event_class'));
});

it('should produce a StructuredDocumentRequestUpdated event', function () {
    $structured_document_request = createStructuredDocumentRequest();
    $entity_types = StructuredDocumentEntityTypeEnum::asArray();
    UpdateStructuredDocumentRequest::run($structured_document_request, ['entity_type' => $entity_types[array_rand($entity_types, 1)]]);
    $structured_document_request->refresh();
    $stored_events = DB::table('stored_events')->get()->toArray();

    $this->assertContains(StructuredDocumentRequestUpdated::class, array_column($stored_events, 'event_class'));
});

it('should delete a structured document on DeleteStructuredDocumentRequest action', function () {
    $structured_document_request = createStructuredDocumentRequest();

    $this->assertEquals(1, StructuredDocumentRequest::count());

    DeleteStructuredDocumentRequest::run($structured_document_request->id);

    $this->assertEquals(0, StructuredDocumentRequest::count());
});

it('should Trash a structured document on TrashStructuredDocumentRequest action', function () {
    $structured_document_request = createStructuredDocumentRequest();

    $this->assertEquals(null, $structured_document_request->deleted_at);

    TrashStructuredDocumentRequest::run($structured_document_request->id);
    $structured_document_request->refresh();

    $this->assertNotEquals(null, $structured_document_request->deleted_at);
});

it('should restore structured document on RestoreStructuredDocumentRequest action', function () {
    $structured_document_request = createStructuredDocumentRequest();

    $this->assertEquals(null, $structured_document_request->deleted_at);

    TrashStructuredDocumentRequest::run($structured_document_request->id);
    $structured_document_request->refresh();

    $this->assertNotEquals(null, $structured_document_request->deleted_at);

    RestoreStructuredDocumentRequest::run($structured_document_request);

    $this->assertEquals(null, $structured_document_request->deleted_at);
});
