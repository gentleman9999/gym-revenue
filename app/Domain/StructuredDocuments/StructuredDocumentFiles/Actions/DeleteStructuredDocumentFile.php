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

class DeleteStructuredDocumentFile
{
    use AsAction;

    public function handle(string $structured_document_file_id): bool
    {
        StructuredDocumentFileAggregate::retrieve($structured_document_file_id)->delete()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-files.delete', StructuredDocumentFile::class);
    }

    public function asController(string $structured_document_file_id): bool
    {
        return $this->handle($structured_document_file_id);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document file was deleted")->flash();

        return Redirect::back();
    }
}
