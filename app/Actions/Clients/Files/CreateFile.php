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


class CreateFile
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'uuid|required',
            'filename' => 'max:255|required',
            'original_filename' => 'max:255|required',
            'extension' => 'required|string|min:3|max:4',
            'bucket' => 'max:255|required',
            'key' => 'max:255|required',
            'permissions' =>'json|nullable|sometimes',
            'size' => 'integer|min:1|required',//TODO: add max size
            'client_id' => 'exists:clients,id|required',
            'user_id' => 'sometimes|nullable|exists:users,id'
        ];
    }

    public function handle($data, $current_user = null)
    {

        FileAggregate::retrieve($data['id'])->create($current_user->id ?? "Auto Generated", $data)->persist();
        return File::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('files.create', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {

        $file = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("File '{$file->filename}' was created")->flash();

//        return Redirect::route('files');
        return Redirect::back();

    }
}
