<?php

namespace Database\Seeders\Data;

use App\Imports\NicknameImport;
use Excel;
use Illuminate\Database\Seeder;

class NicknameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new NicknameImport(), __DIR__ . "/../../../database/data/nicknames.csv");
    }
}
