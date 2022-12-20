<?php

namespace Database\Seeders\Data;

use App\Imports\NicknameImport;
use Excel;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class NicknameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump("Adding Nicknames from CSV");

        Excel::import(new NicknameImport(), __DIR__ . "/../../../database/data/nicknames.csv");

        VarDumper::dump("Adding Nicknames from CSV completed");
    }
}
