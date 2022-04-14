<?php

namespace App\Actions\Clients\Calendar;

use App\Actions\Clients\Files\CreateFile;
use App\Aggregates\Clients\CalendarAggregate;
use App\Aggregates\Clients\ClientAggregate;
use App\Models\Calendar\CalendarEvent;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;

class UploadFile
{
    use AsAction;

    public function rules()
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
            '*.entity_id' => 'string|required'
        ];
    }

    public function handle($data, $current_user = null)
    {
        $files = [];
        foreach( $data as $file){
            $file['entity_type'] = CalendarEvent::class;
            $files[] = CreateFile::run($file);
        }

        return $files;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request)
    {
        $files = $this->handle(
            $request->validated(),
            $request->user(),
        );

        $fileCount = count($files);
        Alert::success("{$fileCount} Files Added to event")->flash();

        return Redirect::route('calendar');
    }

}
