<?php

namespace Database\Seeders\Clients;

use App\Domain\Locations\Projections\Location;

use App\Domain\LocationVendors\Actions\CreateLocationVendor;
use App\Support\Uuid;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class LocationVendorSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = Location::all();

        foreach ($locations as $idx => $location) {
            //$vendor_categories = LocationVendorCategory::all()->toArray();
            $location_vendor['name'] = $this->faker->name;
            $location_vendor['client_id'] = $location->client_id;
            $location_vendor['vendor_category_id'] = Uuid::new(); //$vendor_categories[array_rand($vendor_categories, 1)]['id'];
            $location_vendor['location_id'] = $location->id;
            $location_vendor['poc_name'] = $location->name.'Vendor';
            $location_vendor['poc_email'] = $this->faker->email;
            $location_vendor['poc_phone'] = '';

            VarDumper::dump("Adding {$location_vendor['name']}");
            CreateLocationVendor::run($location_vendor);
        }
    }
}
