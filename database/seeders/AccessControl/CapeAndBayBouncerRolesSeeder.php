<?php

namespace Database\Seeders\AccessControl;

use App\Enums\SecurityGroupEnum;
use Illuminate\Database\Seeder;
use Bouncer;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

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
        collect(SecurityGroupEnum::cases())->keyBy('name')->only(['ADMIN'])->each(function ($enum) {
            Role::create([
                'name' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE),
                'group' => $enum->value
            ])->update(['title' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE)]);
        });
    }

}
