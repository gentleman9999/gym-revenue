<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Actions;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\Domain\StructuredDocuments\StructuredDocumentAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteStructuredDocument
{
    use AsAction;

    public function handle(string $structured_document_id): bool
    {
        StructuredDocumentAggregate::retrieve($structured_document_id)->delete()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-documents.delete', StructuredDocument::class);
    }

    public function asController(string $structured_document_id): bool
    {
        return $this->handle($structured_document_id);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document was deleted")->flash();

        return Redirect::back();
    }
}
