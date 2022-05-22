<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashFile
{
    use AsAction;

    public function handle($id, $current_user = null)
    {
        FileAggregate::retrieve($id)->trash($current_user->id ?? "Auto Generated")->persist();

        return File::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.trash', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $file = $this->handle(
            $id,
            $request->user(),
        );

        Alert::success("File '{$file->filename}' was sent to the trash")->flash();

//        return Redirect::route('files');
        return Redirect::back();
    }
}
