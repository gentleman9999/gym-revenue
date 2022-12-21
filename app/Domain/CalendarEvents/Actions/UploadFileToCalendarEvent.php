<?php

namespace App\Domain\CalendarEvents\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEvents\CalendarEventAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UploadFileToCalendarEvent extends \App\Actions\Clients\Files\CreateFiles
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

    public function handle($data, $current_user = null): array
    {
        $files = [];

        foreach ($data as $key => $file) {
            $files[] = CalendarEventAggregate::retrieve($file['entity_id'])->upload($file)->persist();
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

    public function htmlRepsonse(array $files)
    {
        $fileCount = count($files);

        Alert::success("{$fileCount} Files Added to event")->flash();

        return Redirect::back();
    }
}
