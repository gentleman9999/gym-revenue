<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\FileAggregate;
use App\Domain\Users\Models\User;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

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
            'permissions' => 'json|nullable|sometimes',
            'size' => 'integer|min:1|required',//TODO: add max size
            'user_id' => 'sometimes|nullable|exists:users,id',
            'visibility' => 'sometimes',
        ];
    }

    public function handle($data, ?User $user = null)
    {
        FileAggregate::retrieve($data['id'])->create((string) $user?->id, $data)->persist();

        return File::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.create', File::class);
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
