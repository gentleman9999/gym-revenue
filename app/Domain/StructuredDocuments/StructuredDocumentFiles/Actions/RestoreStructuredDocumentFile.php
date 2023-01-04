<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions;

use App\Domain\StructuredDocuments\StructuredDocumentFiles\Projections\StructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\StructuredDocumentFileAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreStructuredDocumentFile
{
    use AsAction;

    public function handle(StructuredDocumentFile $structured_document_file): StructuredDocumentFile
    {
        StructuredDocumentFileAggregate::retrieve($structured_document_file->id)->restore()->persist();

        return $structured_document_file->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-files.restore', StructuredDocumentFile::class);
    }

    public function asController(StructuredDocumentFile $structured_document_file): StructuredDocumentFile
    {
        return $this->handle($structured_document_file);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document file restored.")->flash();

        return Redirect::back();
    }
}
