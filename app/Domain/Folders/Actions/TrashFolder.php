<?php

declare(strict_types=1);

namespace App\Domain\Folders\Actions;

use App\Actions\GymRevAction;
use App\Domain\Folders\FolderAggregate;
use App\Models\Folder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class TrashFolder extends GymRevAction
{
    /**
     * @param array<string, mixed> $payload
     *
     */
    public function handle(array $payload): Folder
    {
        $id     = $payload['id'];
        $folder = Folder::findOrFail($id);

        FolderAggregate::retrieve($id)->trash()->persist();

        return $folder;
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
        $current_user = $request->user();

        return $current_user->can('folders.trash', Folder::class);
    }

    public function asController(ActionRequest $request, Folder $folder): Folder
    {
        return $this->handle($folder);
    }

    public function htmlResponse(Folder $folder): RedirectResponse
    {
        Alert::success("Folder '{$folder->name}' was trashed")->flash();

        return Redirect::route('files');
    }

    public function trash($folder): Folder
    {
        return $this->handle($folder->id);
    }
}
