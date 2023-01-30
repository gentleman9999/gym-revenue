<?php

namespace App\Domain\Folders\Actions;

use App\Actions\GymRevAction;

use App\Aggregates\Clients\FolderAggregate;
use App\Models\Folder;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class RestoreFolder extends GymRevAction
{
    public function handle($data)
    {
        $id = $data['id'];
        Folder::withTrashed()->findOrFail($id)->restore();
        $folder = Folder::findOrFail($id);
        FolderAggregate::retrieve($id)->restore($data['user_id'] ?? "Auto Generated", $folder)->persist();

        return $folder;
    }

    public function mapArgsToHandle($args): array
    {
        return [$args];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('folders.trash', Folder::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $user = $request->user();
        $user_id = $user->id ?? null;
        $data = [
            'id' => $id,
            'user_id' => $user_id,
        ];
        $folder = $this->handle(
            $data
        );
        Alert::success("File '{$folder->filename}' was restore back")->flash();

        return Redirect::back();
    }
}
