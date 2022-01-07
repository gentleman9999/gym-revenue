<?php

namespace Database\Seeders\Data;

use App\Models\Endusers\Service;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = ['Personal Training', 'Small Group Training (SGPT)', 'Nutrition', 'Spin Class', 'HIIT Class', 'Yoga'];
        foreach ($services as $service) {
            Service::create(['name' => $service]);
            VarDumper::dump("Adding service {$service}");
        }
    }

}
