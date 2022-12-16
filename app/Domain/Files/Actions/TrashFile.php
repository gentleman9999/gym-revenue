<?php

namespace App\Domain\Files\Actions;

use App\Actions\GymRevAction;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class TrashFile extends GymRevAction
{
    public function handle($data)
    {
        $id = $data['id'];
        $user_id = null;
        if (key_exists('user_id', $data)) {
            $user_id = $data['user_id'];
        }
        FileAggregate::retrieve($id)->trash($user_id ?? "Auto Generated")->persist();

        return File::withTrashed()->findOrFail($id);
    }

    public function mapArgsToHandle($args): array
    {
        return [$args];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.trash', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $user = $request->user();
        $user_id = $user->id ?? null;
        $data = [
            'id' => $id,
            'user_id' => $user_id,
        ];
        $file = $this->handle(
            $data
        );

        Alert::success("File '{$file->filename}' was sent to the trash")->flash();

        return Redirect::back();
    }
}
