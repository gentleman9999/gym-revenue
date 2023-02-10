<?php

declare(strict_types=1);

namespace App\Domain\Files\Actions;

use App\Actions\GymRevAction;
use App\Aggregates\Clients\FileAggregate;
use App\Domain\Users\Models\User;
use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateFile extends GymRevAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => 'uuid|required',
            'filename' => 'max:255|required',
            'original_filename' => 'max:255|required',
            'extension' => 'required|string|min:3|max:4',
            'bucket' => 'max:255|required',
            'key' => 'max:255|required',
            'permissions' => 'json|nullable|sometimes',
            'size' => 'integer|min:1|required',//TODO: add max size
            'user_id' => 'sometimes|nullable|exists:users,id',
            'visibility' => 'sometimes',
            'client_id' => 'required',
            'is_public' => 'boolean',
        ];
    }

    public function handle($data, $model, ?User $user = null): File
    {
        FileAggregate::retrieve($data['id'])->create($model, (string) $user?->id, $data)->persist();

        return File::findOrFail($data['id']);
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array<array<string, mixed>>
     */
    public function mapArgsToHandle(array $args): array
    {
        return [$args];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('files.create', File::class);
    }

    public function asController(ActionRequest $request): File
    {
        return $this->handle($request->validated(), $request->user());
    }

    /**
     *
     */
    public function htmlResponse(File $file): RedirectResponse
    {
        Alert::success("File '{$file->filename}' was created")->flash();

        return Redirect::back();
    }
}
