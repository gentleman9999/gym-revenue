<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Actions;

use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\StructuredDocumentRequestAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashStructuredDocumentRequest
{
    use AsAction;

    public function handle(string $structured_document_request_id): bool
    {
        StructuredDocumentRequestAggregate::retrieve($structured_document_request_id)->trash()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-request.trash', StructuredDocumentRequest::class);
    }

    public function asController(string $structured_document_request_id): bool
    {
        return $this->handle($structured_document_request_id);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document request sent to trash")->flash();

        return Redirect::back();
    }
}
