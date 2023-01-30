<?php

namespace App\Domain\Files\Actions;

use App\Actions\GymRevAction;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class RenameFile extends GymRevAction
{
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

    public function handle($data)
    {
        $id = $data['id'];
        FileAggregate::retrieve($id)->rename($data['user_id'] ?? "Auto Generated", $data)->persist();

        return File::findOrFail($id);
    }

    public function mapArgsToHandle($args): array
    {
        return [$args];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.update', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $file = $this->handle(
            $data,
        );

        Alert::success("File '{$file->filename}' was created")->flash();

//        return Redirect::route('files');
        return Redirect::back();
    }
}
