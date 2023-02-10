<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Projections\StructuredDocumentFile;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\StructuredDocumentFileAggregate;
use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateStructuredDocumentFile
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
            'file_id' => ['required', 'string'],
            'entity_id' => ['required', 'string'],
            'entity_type' => ['required', 'string', new Enum(StructuredDocumentEntityTypeEnum::class)],
            'structured_document_id' => ['required', 'string', 'exists:structured_documents,id'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): StructuredDocumentFile
    {
        $structured_document = StructuredDocument::select('ttl')->whereId($data['structured_document_id'])->first();
        $data['expires_at']  = $structured_document->ttl;

        $id = Uuid::get();
        StructuredDocumentFileAggregate::retrieve($id)->create($data)->persist();

        return StructuredDocumentFile::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-files.create', StructuredDocumentFile::class);
    }

    public function asController(ActionRequest $request): StructuredDocumentFile
    {
        return $this->handle($request->validated());
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document file was created")->flash();

        return Redirect::back();
    }
}
