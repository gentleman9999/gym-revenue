<?php

namespace Database\Seeders\AccessControl;

use App\Enums\SecurityGroupEnum;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;

class CapeAndBayBouncerRolesSeeder extends Seeder
{
    protected $teams;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [];
        foreach (SecurityGroupEnum::cases() as $role) {
            $roles[] = [
                'name' => $name = mb_convert_case(str_replace('_', ' ', $role->name), MB_CASE_TITLE),
                'group' => $role->value,
                'title' => $name,
            ];
        }
        Role::upsert($roles, ['name']);
    }
}
