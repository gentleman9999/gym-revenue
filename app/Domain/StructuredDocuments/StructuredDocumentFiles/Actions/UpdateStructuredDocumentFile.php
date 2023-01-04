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

class UpdateStructuredDocumentFile
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
            'file_id' => ['sometimes', 'string'],
            'entity_id' => ['sometimes', 'string'],
            'entity_type' => ['sometimes', 'string'],
            'structured_document_id' => ['sometimes', 'string', 'exists:structured_documents,id'],
        ];
    }

    public function handle(StructuredDocumentFile $structured_document_file, array $data): StructuredDocumentFile
    {
        StructuredDocumentFileAggregate::retrieve($structured_document_file->id)
            ->update($data)
            ->persist();

        return $structured_document_file->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-document-files.update', StructuredDocumentFile::class);
    }

    public function asController(ActionRequest $request, StructuredDocumentFile $structured_document_file): StructuredDocumentFile
    {
        return $this->handle(
            $structured_document_file,
            $request->validated()
        );
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document file was updated")->flash();

        return Redirect::back();
    }
}
