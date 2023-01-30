<?php

declare(strict_types=1);

namespace App\Domain\Users\Reactors;

use App\Actions\Clients\Files\CreateFile;
use App\Domain\LocationEmployees\Actions\CreateLocationEmployee;
use App\Domain\Users\Actions\UpdateUser;
use App\Domain\Users\Events\FileUploaded;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UsersImported;
use App\Domain\Users\Events\UserUpdated;
use App\Domain\Users\Models\User;
use App\Domain\Users\Services\UserTypeDeterminer;
use App\Imports\UsersImport;
use App\Imports\UsersImportWithHeader;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class UserReactor extends Reactor implements ShouldQueue
{
//    public function onWelcomeEmailSent(WelcomeEmailSent $event)
//    {
//        $user = User::find($event->user);
//        Mail::to($user->email)->send(new NewUserWelcomeEmail($user));
//    }
//

    public function onUserImported(UsersImported $event): void
    {
        $headings = (new HeadingRowImport())->toArray($event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        if (in_array($headings[0][0][0], (new User())->getFillable())) {
            Excel::import(new UsersImportWithHeader($event->clientId()), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        } else {
            Excel::import(new UsersImport($event->clientId()), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    public function onFileUploaded(FileUploaded $event): void
    {
        $data = $event->payload;
        $model = User::find($data['user_id']);

        CreateFile::run($data, $model, User::find($event->userId()));
    }

    public function onUserWrite(UserCreated|UserUpdated $event): void
    {
        DB::transaction(function () use ($event) {
            $user = User::findOrFail($event->aggregateRootUuid());
            $data = $event->payload ?? [];
            $this->syncLocationEmployeeData($user, $data);

            $user_type = UserTypeDeterminer::getUserType($user);
            if ($user_type !== $user->user_type && ! is_null($user->client_id)) {
                $data['user_type'] = $user_type;
                UpdateUser::run($user, $data);
            }
        });
    }

    protected function syncLocationEmployeeData(User $user, array $data): void
    {
        if (array_key_exists('departments', $data)) {
            $location_employee_data['location_id'] = '';
            $location_employee_data['client_id'] = $user->client_id;
            $location_employee_data['user_id'] = $user->id;
            $location_employee_data['primary_supervisor_user_id'] = '';

            if (count($data) != count($data, COUNT_RECURSIVE)) {
                if (array_key_exists('departments', $data)) {
                    foreach ($data['departments'] as $dept) {
                        $location_employee_data['department_id'] = $dept['department'];
                        $location_employee_data['position_id'] = $dept['position'];
                        CreateLocationEmployee::run($location_employee_data);
                    }
                }
            }
        }
    }
}
