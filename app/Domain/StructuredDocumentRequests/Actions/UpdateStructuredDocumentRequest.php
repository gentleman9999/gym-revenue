<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Actions;

use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\StructuredDocumentRequestAggregate;
use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateStructuredDocumentRequest
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'structured_document_id' => ['sometimes', 'string', 'exists:structured_documents,id'],
            'entity_id' => ['sometimes', 'string'],
            'entity_type' => ['sometimes', 'string', new Enum(StructuredDocumentEntityTypeEnum::class)],
        ];
    }

    public function handle(StructuredDocumentRequest $structured_document_request, array $data): StructuredDocumentRequest
    {
        StructuredDocumentRequestAggregate::retrieve($structured_document_request->id)->update($data)->persist();

        return $structured_document_request->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-request.update', StructuredDocumentRequest::class);
    }

    public function asController(ActionRequest $request, StructuredDocumentRequest $structured_document_request): StructuredDocumentRequest
    {
        return $this->handle(
            $structured_document_request,
            $request->validated()
        );
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("structured document request was updated")->flash();

        return Redirect::back();
    }
}
