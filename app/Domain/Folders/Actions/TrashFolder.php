<?php

namespace App\Domain\Folders\Actions;

use App\Domain\Folders\FolderAggregate;
use App\Models\Folder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashFolder
{
    use AsAction;

    public function handle(string $id): Folder
    {
        $folder = Folder::findOrFail($id);

        FolderAggregate::retrieve($id)->trash()->persist();

        return $folder;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('folders.trash', Folder::class);
    }

    public function asController(ActionRequest $request, Folder $folder): Folder
    {
        return $this->handle(
            $folder->id
        );
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
