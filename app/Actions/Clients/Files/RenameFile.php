<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RenameFile
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
            'client_id' => 'exists:clients,id|required',
            'user_id' => 'sometimes|nullable|exists:users,id',
        ];
    }

    public function handle($data, $current_user = null)
    {
        FileAggregate::retrieve($data['id'])->rename($current_user->id ?? "Auto Generated", $data)->persist();

        return File::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.update', File::class);
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
