<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Actions;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\Domain\StructuredDocuments\StructuredDocumentAggregate;
use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateStructuredDocument
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
            'entity_id' => ['sometimes', 'string'],
            'entity_type' => ['sometimes', 'string', new Enum(StructuredDocumentEntityTypeEnum::class)],
            'template_file_id' => ['sometimes', 'string'],
            'ttl' => 'sometimes',
        ];
    }

    public function handle(StructuredDocument $structured_document, array $data): StructuredDocument
    {
        StructuredDocumentAggregate::retrieve($structured_document->id)
            ->update($data)
            ->persist();

        return $structured_document->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-documents.update', StructuredDocument::class);
    }

    public function asController(ActionRequest $request, StructuredDocument $structured_document): StructuredDocument
    {
        return $this->handle(
            $structured_document,
            $request->validated()
        );
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document was updated")->flash();

        return Redirect::back();
    }
}
