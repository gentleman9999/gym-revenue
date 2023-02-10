<?php

declare(strict_types=1);

namespace App\Domain\CalendarEvents\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEvents\CalendarEventAggregate;
use App\Domain\Files\Actions\CreateFiles;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UploadFileToCalendarEvent extends CreateFiles
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

    /**
     * @param array<string, mixed> $data
     * @param User|null            $_
     *
     * @return array<CalendarEventAggregate>
     */
    public function handle(array $data, ?User $_ = null): array
    {
        $files = [];

        foreach ($data as $file) {
            $files[] = CalendarEventAggregate::retrieve($file['entity_id'])->upload($file)->persist();
        }

        return $files;
    }

    /**
     * @return string[]
     */
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
