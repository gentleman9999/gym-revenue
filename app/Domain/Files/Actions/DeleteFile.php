<?php

declare(strict_types=1);

namespace App\Domain\Files\Actions;

use App\Actions\GymRevAction;
use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class DeleteFile extends GymRevAction
{
    public function handle($data)
    {
        $id      = $data['id'];
        $deleted = File::withTrashed()->findOrFail($id);
        FileAggregate::retrieve($id)->delete($data['user_id'] ?? "Auto Generated", $deleted)->persist();

        return $deleted;
    }

    public function mapArgsToHandle($args): array
    {
        return [$args];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.delete', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $user    = $request->user();
        $user_id = $user->id ?? null;
        $data    = [
            'id' => $id,
            'user_id' => $user_id,
        ];

        $file = $this->handle($data);

        Alert::success("File '{$file->filename}' was deleted")->flash();

//        return Redirect::route('files');
        return Redirect::back();
    }
}
