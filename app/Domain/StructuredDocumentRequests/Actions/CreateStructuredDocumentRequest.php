<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Actions;

use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\Domain\StructuredDocumentRequests\StructuredDocumentRequestAggregate;
use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateStructuredDocumentRequest
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
            'client_id' => ['required', 'string', 'exists:clients,id'],
            'structured_document_id' => ['required', 'string', 'exists:structured_documents,id'],
            'entity_id' => ['required', 'string'],
            'entity_type' => ['required', 'string', new Enum(StructuredDocumentEntityTypeEnum::class)],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): StructuredDocumentRequest
    {
        $id = Uuid::get();
        StructuredDocumentRequestAggregate::retrieve($id)->create($data)->persist();

        return StructuredDocumentRequest::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-request.create', StructuredDocumentRequest::class);
    }

    public function asController(ActionRequest $request): StructuredDocumentRequest
    {
        return $this->handle($request->validated());
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document request was created")->flash();

        return Redirect::back();
    }
}
