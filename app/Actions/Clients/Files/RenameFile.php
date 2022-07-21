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
            'filename' => 'max:255|required',
//            'client_id' => 'exists:clients,id|required',
            'user_id' => 'sometimes|nullable|exists:users,id',
        ];
    }

    public function handle($id, $data, $current_user = null)
    {
        FileAggregate::retrieve($id)->rename($current_user->id ?? "Auto Generated", $data)->persist();

        return File::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.update', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->client_id;//TODO: remove for being injected by middleware
        $file = $this->handle(
            $id,
            $data,
            $request->user(),
        );

        Alert::success("File '{$file->filename}' was created")->flash();

//        return Redirect::route('files');
        return Redirect::back();
    }
}
