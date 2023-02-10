<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Actions;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\Domain\StructuredDocuments\StructuredDocumentAggregate;
use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateStructuredDocument
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
            'entity_id' => ['required', 'string'],
            'entity_type' => ['required', 'string', new Enum(StructuredDocumentEntityTypeEnum::class)],
            'template_file_id' => ['sometimes', 'string'],
            'ttl' => 'required',
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): StructuredDocument
    {
        $id = Uuid::get();
        StructuredDocumentAggregate::retrieve($id)->create($data)->persist();

        return StructuredDocument::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('structured-documents.create', StructuredDocument::class);
    }

    public function asController(ActionRequest $request): StructuredDocument
    {
        return $this->handle($request->validated());
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Structured document was created")->flash();

        return Redirect::back();
    }
}
