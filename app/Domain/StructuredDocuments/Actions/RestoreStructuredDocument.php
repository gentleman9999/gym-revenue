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

class RestoreStructuredDocument
{
    use AsAction;

    public function handle(StructuredDocument $structured_document): StructuredDocument
    {
        StructuredDocumentAggregate::retrieve($structured_document->id)->restore()->persist();

        return $structured_document->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-documents.restore', StructuredDocument::class);
    }

    public function asController(StructuredDocument $structured_document): StructuredDocument
    {
        return $this->handle($structured_document);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document restored.")->flash();

        return Redirect::back();
    }
}
