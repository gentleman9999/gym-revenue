<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Clients\FileAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Helpers\Uuid;
use App\Models\Clients\Location;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Console\Command;


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
        return $current_user->can('files.trash', File::class);
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
