<?php

namespace Database\Seeders\Users;

use App\Models\User;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

abstract class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump("Adding {$this->type} Users...");

        foreach($this->getUsersToAdd() as $idx => $user)
        {
            $user_record = User::whereEmail($user['email'])->first();
            if(is_null($user_record))
            {
                VarDumper::dump("Adding {$user['first_name']} {$user['last_name']}");
                $this->addUser($user);
            }
            else
            {
                VarDumper::dump("Skipping {$user['name']}!");
            }

        }
    }
}
