<?php

namespace App\Domain\Folders\Actions;

use App\Aggregates\Clients\FolderAggregate;
use App\Models\Folder;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreFolder
{
    use AsAction;

    public function handle($id, $current_user = null)
    {
        Folder::withTrashed()->findOrFail($id)->restore();
        $folder = Folder::findOrFail($id);
        FolderAggregate::retrieve($id)->restore($current_user->id ?? "Auto Generated", $folder)->persist();

        return $folder;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('folders.trash', Folder::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $folder = $this->handle(
            $id,
            $request->user(),
        );
        Alert::success("File '{$folder->filename}' was restore back")->flash();

        return Redirect::back();
    }
}
