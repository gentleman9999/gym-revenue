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

class DeleteFolder extends GymRevAction
{
    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): Folder
    {
        $id     = $data['id'];
        $folder = Folder::findOrFail($id);

        FolderAggregate::retrieve($id)->delete()->persist();

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

        return $current_user->can('folders.delete', Folder::class);
    }

    public function asController(ActionRequest $request, Folder $folder): Folder
    {
        $data = [
            'id' => $folder->id,
        ];

        return $this->handle(
            $data
        );
    }

    public function htmlResponse(Folder $folder): RedirectResponse
    {
        Alert::success("Folder '{$folder->name}' was deleted")->flash();

        return Redirect::route('files');
    }

    public function delete($folder): Folder
    {
        return $this->handle($folder->id);
    }
}
