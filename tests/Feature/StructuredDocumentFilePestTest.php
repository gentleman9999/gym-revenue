<?php

declare(strict_types=1);

use App\Domain\StructuredDocuments\Actions\CreateStructuredDocument;
use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\CreateStructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\DeleteStructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\RestoreStructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\TrashStructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\UpdateStructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileCreated;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileUpdated;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Projections\StructuredDocumentFile;

beforeEach(function () {
    //
});

function createStructuredDocumentFromFile(array $attributes = []): StructuredDocument
{
    return CreateStructuredDocument::run($attributes + StructuredDocument::factory()->raw());
}

function createStructuredDocumentFile(array $attributes = []): StructuredDocumentFile
{
    return CreateStructuredDocumentFile::run($attributes + StructuredDocumentFile::factory()->raw() + [
        'structured_document_id' => createStructuredDocumentFromFile()->id,
    ]);
}

it('should produce a StructuredDocumentFileCreated event', function () {
    createStructuredDocumentFile();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(StructuredDocumentFileCreated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a StructuredDocumentFileUpdated event', function () {
    $structuredDocumentFile = createStructuredDocumentFile();
    UpdateStructuredDocumentFile::run($structuredDocumentFile, ['expires_at' => now()]);
    $structuredDocumentFile->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(StructuredDocumentFileUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a structured document file on DeleteStructuredDocumentFile action', function () {
    $structuredDocumentFile = createStructuredDocumentFile();

    $this->assertEquals(1, StructuredDocumentFile::count());

    DeleteStructuredDocumentFile::run($structuredDocumentFile->id);

    $this->assertEquals(0, StructuredDocumentFile::count());
});

it('should Trash a structured document file on TrashStructuredDocumentFile action', function () {
    $structuredDocumentFile = createStructuredDocumentFile();

    $this->assertEquals(null, $structuredDocumentFile->deleted_at);

    TrashStructuredDocumentFile::run($structuredDocumentFile->id);
    $structuredDocumentFile->refresh();

    $this->assertNotEquals(null, $structuredDocumentFile->deleted_at);
});

it('should restore a structured document file on RestoreStructuredDocumentFile action', function () {
    $structuredDocumentFile = createStructuredDocumentFile();

    $this->assertEquals(null, $structuredDocumentFile->deleted_at);

    TrashStructuredDocumentFile::run($structuredDocumentFile->id);
    $structuredDocumentFile->refresh();

    $this->assertNotEquals(null, $structuredDocumentFile->deleted_at);

    RestoreStructuredDocumentFile::run($structuredDocumentFile);

    $this->assertNotEquals(null, $structuredDocumentFile->deleted_at);
});
