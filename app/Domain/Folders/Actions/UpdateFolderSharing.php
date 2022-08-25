<?php

namespace App\Domain\Folders\Actions;

use App\Domain\Folders\FolderAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\Folder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateFolderSharing
{
    use AsAction;

    public function handle(string $id, array $payload): Folder
    {
        $payload['id'] = $id;
        FolderAggregate::retrieve($id)->updateSharing($payload)->persist();

        return Folder::findOrFail($id);
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],
            'team_ids' => ['array', 'sometimes'],
            'location_ids' => ['array', 'sometimes'],
            'user_ids' => ['array', 'sometimes'],
            'position_ids' => ['array', 'sometimes'],
            'department_ids' => ['array', 'sometimes'],
            'role_ids' => ['array', 'sometimes'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('folders.update', Folder::class);
    }

    public function asController($id, ActionRequest $request): Folder
    {
        return $this->handle(
            $id,
            $request->validated()
        );
    }

    public function htmlResponse(Folder $folder): RedirectResponse
    {
        Alert::success("Folder '{$folder->name}' was updated")->flash();

        //return Redirect::route('folder.edit', $folder->id);
        return Redirect::back();
    }

    public function create($user, array $input): Folder
    {
        return $this->handle($input);
    }
}
