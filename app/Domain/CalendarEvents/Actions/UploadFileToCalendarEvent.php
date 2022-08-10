<?php

namespace App\Domain\CalendarEvents\Actions;

use App\Actions\Clients\Files\CreateFile;
use App\Domain\CalendarEvents\CalendarEvent;
use App\Http\Middleware\InjectClientId;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UploadFileToCalendarEvent
{
    use AsAction;

    public function rules(): array
    {
        return [
            '*.id' => 'uuid|required',
            '*.filename' => 'max:255|required',
            '*.original_filename' => 'max:255|required',
            '*.extension' => 'required|string|min:3|max:4',
            '*.bucket' => 'max:255|required',
            '*.key' => 'max:255|required',
            '*.size' => 'integer|min:1|required',//TODO: add max size
            '*.client_id' => 'exists:clients,id|required',
            '*.user_id' => 'nullable|exists:users,id',
            '*.entity_id' => 'string|required',
        ];
    }

    public function handle(array $data): array
    {
        $files = [];
        foreach ($data as $file) {
            $file['entity_type'] = CalendarEvent::class;
            $files[] = CreateFile::run($file);
        }

        return $files;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request): array
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlRepsonse(array $files)
    {
        $fileCount = count($files);

        Alert::success("{$fileCount} Files Added to event")->flash();

        return Redirect::back();
    }
}
