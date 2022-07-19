<?php

namespace App\Domain\Teams\Folders;

use App\Domain\Folders\FolderAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\Folder;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateFolder implements CreatesTeams
{
    use AsAction;

    public function handle(array $payload): Folder
    {
        $id = Uuid::new();

        FolderAggregate::retrieve($id)->create($payload)->persist();

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
            'name' => ['required', 'max:50'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.create', Folder::class);
    }

    public function asController(ActionRequest $request): Folder
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(Folder $folder): RedirectResponse
    {
        Alert::success("Folder '{$folder->name}' was created")->flash();

        return Redirect::route('folder.edit', $folder->id);
    }

    public function create($user, array $input): Folder
    {
        return $this->handle($input);
    }
}
