<?php

namespace Database\Seeders\Users;

use App\Domain\Teams\Actions\CreateTeam;
use App\Services\Process;
use Illuminate\Database\Seeder;

abstract class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $process = Process::allocate(3);
        $team_id = CreateTeam::run([
            'client_id' => null,
            'name' => 'Cape & Bay Admin Team',
            'home_team' => 1,
        ])->id;


        foreach ($this->getUsersToAdd() as $user) {
            // \Database\Seeders\Users\CapeAndBayUserSeeder::addUser
            $process->queue([static::class, 'addUser'], $user, $team_id);
        }

        $process->run();
    }
}
