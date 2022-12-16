<?php

namespace App\Domain\Files\Actions;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteFile
{
    use AsAction;

    public function handle($id, $current_user = null)
    {
        $deleted = File::withTrashed()->findOrFail($id);
        FileAggregate::retrieve($id)->delete($current_user->id ?? "Auto Generated", $deleted)->persist();

        return $deleted;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.delete', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $file = $this->handle(
            $id,
            $request->user(),
        );

        Alert::success("File '{$file->filename}' was deleted")->flash();

//        return Redirect::route('files');
        return Redirect::back();
    }
}
